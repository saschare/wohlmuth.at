<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_ExistsInLanguage extends Aitsu_Article_Policy_Abstract {

	protected function _evalStatement($statement) {

		$langs = explode(' ', $statement);

		return (object) array (
			'source' => Aitsu_Db :: fetchOne('' .
			'select t.idlang ' .
			'from _lang t ' .
			'left join _lang s on t.idclient = s.idclient ' .
			'where ' .
			'	t.name = :name ' .
			'	and s.idlang = :idlang', array (
				':name' => $langs[0],
				':idlang' => Aitsu_Registry :: get()->env->idlang
			)),
			'target' => Aitsu_Db :: fetchOne('' .
			'select t.idlang ' .
			'from _lang t ' .
			'left join _lang s on t.idclient = s.idclient ' .
			'where ' .
			'	t.name = :name ' .
			'	and s.idlang = :idlang', array (
				':name' => $langs[1],
				':idlang' => Aitsu_Registry :: get()->env->idlang
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

		if (Aitsu_Db :: fetchOne('' .
			'select idlang from _art_lang where idartlang = :idartlang', array (
				':idartlang' => $this->_idartlang
			)) != $this->_statement->source) {
			/*
			 * The current article is not of the specified source language.
			 * The policy is therefore assumed to be fullfilled.
			 */
			trigger_error(var_export(array (
				'statement' => $this->_rawStatement,
				'source' => $this->_statement->source
			), true));
			$this->_message = 'not concerned.';
			return true;
		}

		if (!$this->_statement->target) {
			/*
			 * The specified target language does not|no longer|not yet exist. 
			 * The policy is therefore assumed not to be fullfilled.
			 */
			$this->_message = 'target language does not exist.';
			return false;
		}

		$status = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	orig.online origonline, ' .
		'	target.online targetonline, ' .
		'	target.idartlang ' .
		'from _art_lang orig ' .
		'left join _pubv_art_lang target on orig.idart = target.idart and target.idlang = :idlang ' .
		'where ' .
		'	orig.idartlang = :idartlang ' .
		'	and target.idartlang is not null', array (
			':idartlang' => $this->_idartlang,
			':idlang' => $this->_statement->target
		));

		if (!$status) {
			$this->_message = 'target article does not exist.';
			return false;
		}

		/*
		 * Set the target idartartlang as a dependency.
		 */
		$this->_dependencies = array (
			$status['idartlang']
		);

		if ($status['origonline'] == $status['targetonline']) {
			return true;
		}

		if ($status['targetonline'] == 1) {
			$this->_message = 'target article should be offline.';
		} else {
			$this->_message = 'target article should be online.';
		}

		return false;
	}

}