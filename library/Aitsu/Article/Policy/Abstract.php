<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Aitsu_Article_Policy_Abstract {

	/*
	 * The idartlang the policy belongs to.
	 */
	protected $_idartlang = null;

	/*
	 * The statement (i.e. the parameters) bound to
	 * the policy. It is up to the concrete class to
	 * evaluate the statement to get reasonable values.
	 */
	protected $_statement = null;

	/*
	 * The message that belongs to the error (if any).
	 */
	protected $_message = '';

	/*
	 * Default check interval in seconds.
	 */
	protected $_checkInterval = 86400;

	/*
	 * Known dependencies from other articles. It is null or
	 * an array of idartlangs and should be populated from
	 * within the _evaluteStatement method.
	 */
	protected $_dependencies = null;

	/**
	 * @param String Statement
	 * @param Integer Idartlang of the article the policy belongs to.
	 */
	public final function __construct($statement, $idartlang = null) {

		$this->_idartlang = $idartlang;
		$this->_statement = $this->_evalStatement($statement);
	}

	/**
	 * @param Void 
	 * @return Boolean True, if the policy is fullfilled. False otherwise.
	 */
	public final function isFullfilled() {

		try {
			$status = $this->_persistPolicy($this->_isFullfilled());
		} catch (Exception $e) {
			$status = false;
		}

		return $status;
	}

	/**
	 * This method has to be implemented by the concrete class.
	 * @param Void 
	 * @return Boolean True, if the policy is fullfilled. False otherwise.
	 */
	abstract protected function _isFullfilled();

	/**
	 * @param Void 
	 * @return String The message bound to the error. If the policy fullfills, null is returned.
	 */
	public function getMessage() {

		if ($this->isFullfilled()) {
			return null;
		}

		return strtoupper($this->_message);
	}

	/**
	 * Evaluates the given statement to form the parameters needed to check
	 * whether or not the policy fullfills.
	 * @param String Statement bound to the policy.
	 * @return Mixed The result of the evaluation.
	 */
	protected function _evalStatement($statement) {

		return $statement;
	}

	protected final function _persistPolicy($status) {

		$policyid = Aitsu_Db :: fetchOne('' .
		'select policyid from _policy where policy = :name', array (
			':name' => get_class($this)
		));

		if (!$policyid) {
			$policyid = Aitsu_Db :: query('' .
			'insert into _policy (policy) values (:policy)', array (
				':policy' => get_class($this)
			))->getLastInsertId();
		}

		$data = array (
			':idartlang' => $this->_idartlang,
			':policyid' => $policyid,
			':status' => $status ? 1 : 0,
			':interval' => $this->_checkInterval
		);

		if (Aitsu_Db :: query('' .
			'update _policy set ' .
			'	status = :status, ' .
			'	message = :message, ' .
			'	lastcheck = now(), ' .
			'	nextcheck = date_add(now(), interval :interval second) ' .
			'where ' .
			'	idartlang = :idartlang ' .
			'	and policyid = :policyid', $data)->rowCount() == 0) {
			Aitsu_Db :: query('' .
			'insert into _policy_art ' .
			'(idartlang, policyid, status, message, lastcheck, nextcheck) ' .
			'values ' .
			'(:idartlang, :policyid, :status, :message, now(), date_add(now(), interval :interval second))', $data);
		}

		if ($this->_dependencies != null) {
			$policyartid = Aitsu_Db :: fetchOne('' .
			'select policyartid from _policy_art ' .
			'where ' .
			'	idartlang = :idartlang ' .
			'	and policyid = :policyid', array (
				':idartlang' => $this->_idartlang,
				':policyid' => $policyid
			));
			if ($policyartid) {
				Aitsu_Db :: query('' .
				'delete from _policy_art_dependency ' .
				'where ' .
				'	policyartid = :policyartid', array (
					':policyartid' => $policyartid
				));
				foreach ($this->_dependencies as $dep) {
					try {
						Aitsu_Db :: query('' .
						'insert into _policy_art_dependency ' .
						'(policyartid, idartlang) ' .
						'values ' .
						'(:policyartid, idartlang)', array (
							':policyartid' => $policyartid,
							':idartlang' => $this->_idartlang
						));
					} catch (Exception $e) {
						/*
						 * Do nothing, but prevent the loop from
						 * being broken.
						 */
					}
				}
			}
		}

		return $status;
	}

}