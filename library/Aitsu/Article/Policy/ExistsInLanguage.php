<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_ExistsInLanguage extends Aitsu_Article_Policy_Abstract {

	protected function _evalStatement($statement) {

		return (object) array (
			'idlang' => Aitsu_Db :: fetchOne('' .
			'select idlang from _lang where name = :name', array (
				':name' => $statement
			))
		);
	}

	public function isFullfilled() {

		/*
		 * Following conditions have to be met to fullfill:
		 * 
		 * - the referenced article exists,
		 * - the referenced article is online if the current article is online,
		 * - the referenced article is offline if the current article is offline,
		 * - the referenced article is published
		 */
		 
		
	}

}