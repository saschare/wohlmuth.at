<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Dropcopydb.php 18689 2010-09-10 10:51:21Z akm $}
 */

class Adm_Script_Dropcopydb extends Aitsu_Adm_Script_Abstract {

	public static function getName() {

		return Aitsu_Translate :: translate('Drop the copy from the database');
	}

	public function doDrop() {

		if (empty (Aitsu_Registry :: get()->config->database->params->tblprefixbk)) {
			throw new Exception(Aitsu_Translate :: translate('No backup table prefix specified.'));
		}

		$tables = Aitsu_Db :: fetchCol('' .
		'show tables like ?', array (
			Aitsu_Registry :: get()->config->database->params->tblprefixbk . '%'
		));

		if (!$tables) {
			throw new Exception(Aitsu_Translate :: translate('There are no tables to be dropped.'));
		}

		$bkTables = Aitsu_Db :: fetchCol('' .
		'show tables like ?', array (
			Aitsu_Registry :: get()->config->database->params->tblprefixbk . '%'
		));
		foreach ($bkTables as $bkTable) {
			Aitsu_Db :: query('drop table ' . $bkTable);
		}

		return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Tables dropped.'));
	}
}