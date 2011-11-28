<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_HasPageTitle extends Aitsu_Article_Policy_Abstract {

	protected function _isFullfilled() {

		/*
		 * Following conditions have to be met to fullfill:
		 * 
		 * - the article must have a page title.
		 */

		$pageTitle = Aitsu_Db :: fetchOne('' .
		'select pagetitle from _art_lang where idartlang = :idartlang', array (
			':idartlang' => $this->_idartlang
		));
		
		if (strlen(trim($pageTitle)) > 0) {
			return true;
		}

		$this->_message = 'no page title or white space only.';

		return false;
	}

}