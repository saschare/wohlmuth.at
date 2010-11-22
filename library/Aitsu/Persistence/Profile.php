<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Profile.php 19647 2010-11-03 09:59:32Z akm $}
 */

class Aitsu_Persistence_Profile extends Aitsu_Persistence_Abstract {

	protected $_id = null;

	protected function __construct() {

		$this->_id = Aitsu_Registry :: get()->env->idartlang;
	}

	public function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance[$id] = new self();
		}

		return $instance[$id];
	}

	public function load($reload = false) {

		return $this;
	}

	public function __get($key) {

		return null;
	}

	public function __set($key, $value) {
		/*
		 * Method not implemented.
		 */
	}

	public function save() {

		if (Aitsu_Registry :: isEdit()) {
			return;
		}

		$sum = (microtime(true) - REQUEST_START) * 1000;

		try {
			Aitsu_Db :: query('' .
			'insert into _performance_profile (idartlang, obsdate, tsum, tssum, fastest, slowest, n) ' .
			'values (:idartlang, :date, :sum, :ssum, :sum, :sum, 1) ' .
			'on duplicate key update ' .
			'	tsum = tsum + :sum, ' .
			'	tssum = tssum + :ssum, ' .
			'	n = n + 1, ' .
			'	fastest = least(fastest, :sum), ' .
			'	slowest = greatest(slowest, :sum)', array (
				':sum' => $sum,
				':ssum' => pow($sum, 2),
				':idartlang' => $this->_id,
				':date' => date('Y-m-d')
			));
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

		return $this;
	}

	public function remove() {

		return $this;
	}

}