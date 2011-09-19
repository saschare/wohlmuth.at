<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_IsPublished extends Aitsu_Article_Policy_Abstract {

	protected function _isFullfilled() {

		/*
		 * Following conditions have to be met to fullfill:
		 * 
		 * - the status of the current article must be pushlished.
		 */

		if (Aitsu_Db :: fetchOne('' .
			'select count(p.idartlang) ' .
			'from _art_lang a ' .
			'left join _pub p on a.idartlang = p.idartlang and a.lastmodified = p.pubtime ' .
			'where ' .
			'	a.idartlang = :idartlang', array (
				':idartlang' => $this->_idartlang
			)) > 0) {
			return true;
		}

		return false;
	}

}