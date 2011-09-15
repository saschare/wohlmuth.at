<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_ExistsInLanguage extends Aitsu_Article_Policy_Abstract {

	protected function _evalStatement($statement) {

		return (object) array (
			'idlang' => Aitsu_Db :: fetchOne('' .
			'select idlang from _lang ' .
			'where ' .
			'	name = :name ' .
			'	and idclient = :client', array (
				':name' => $statement,
				':client' => Aitsu_Registry :: get()->env->idclient
			))
		);
	}

	protected function _isFullfilled() {

		/*
		 * Following conditions have to be met to fullfill:
		 * 
		 * - the referenced article exists,
		 * - the referenced article is online if the current article is online,
		 * - the referenced article is offline if the current article is offline,
		 * - the referenced article is published
		 */
		 
		if (!$this->_statement->idlang) {
			/*
			 * The specified language does not|no longer|not yet exist. The policy
			 * is therefore assumed to be fullfilled.
			 */
			return true;
		}

		$status = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	orig.online origonline, ' .
		'	target.online targetonline ' .
		'from _art_lang orig ' .
		'left join _pubv_art_lang target on orig.idart = target.idart and target.idlang = :idlang ' .
		'where ' .
		'	orig.idartlang = :idartlang ' .
		'	and target.idartlang is not null', array (
			':idartlang' => $this->_idartlang,
			':idlang' => $this->_statement->idlang
		));

		if ($status && $status['origonline'] == $status['targetonline']) {
			return true;
		}

		if (!$status) {
			$this->_message = 'target article does not exist.';
		}

		if ($status['targetonline'] == 1) {
			$this->_message = 'target article should be offline.';
		} else {
			$this->_message = 'target article should be online.';
		}

		return false;
	}

}