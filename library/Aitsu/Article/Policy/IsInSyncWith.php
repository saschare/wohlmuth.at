<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 * 
 * This class has not yet been tested.
 */
class Aitsu_Article_Policy_IsInSyncWith extends Aitsu_Article_Policy_Abstract {

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
		 * - the base article is online if the current article is online,
		 * - the base article is offline if the current article is offline,
		 * - the publication time of the base article is older than that
		 *   of the current article. 
		 */

		if (Aitsu_Db :: fetchOne('' .
			'select idlang from _art_lang where idartlang = :idartlang', array (
				':idartlang' => $this->_idartlang
			)) != $this->_statement->source) {
			/*
			 * The current article is not of the specified source language.
			 * The policy is therefore assumed to be fullfilled.
			 */
			$this->_message = 'not concerned.';
			return true;
		}

		if (!$this->_statement->target) {
			/*
			 * The specified target language does not|no longer|not yet exist. 
			 * The policy is therefore assumed to be fullfilled.
			 */
			$this->_message = 'the base article does not exist.';
			return true;
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
			$this->_message = 'the base article does not exist.';
			return true;
		}

		/*
		 * Set the target idartartlang as a dependency.
		 */
		$this->_dependencies = array (
			$status['idartlang']
		);

		if ($status['origonline'] != $status['targetonline']) {
			if ($status['targetonline'] == 1) {
				$this->_message = 'the article should be online.';
			} else {
				$this->_message = 'the article should be offline.';
			}
			return false;
		}

		if (Aitsu_Db :: fetchOne('' .
			'select count(t.idartlang) ' .
			'from _pub s ' .
			'left join _art_lang sartlang on sartlang.idartlang = s.idartlang ' .
			'left join _art_lang tartlang on sartlang.idart = tartlang.idart ' .
			'left join _pub t on tartlang.idartlang = t.idartlang ' .
			'where ' .
			'	tartlang.idlang = :idlang ' .
			'	s.idartlang = :idartlang ' .
			'	and s.status = 1 ' .
			'	and t.status = 1 ' .
			'	and s.pubtime > t.pubtime', array (
				':idartlang' => $this->_idartlang,
				':idlang' => $this->_statement->target
			)) == 0) {
			$this->_message = 'the article \'s publication date is younger that that of the base article.';
		}

		return true;
	}

}