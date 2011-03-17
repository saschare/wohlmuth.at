<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Checkdb.php 18670 2010-09-09 16:50:40Z akm $}
 */

class Adm_Script_Copydb extends Aitsu_Adm_Script_Abstract {

	protected $_tables;

	public static function getName() {

		return Aitsu_Translate :: translate('Copy database within database');
	}

	protected function _hasNext() {

		if (empty (Aitsu_Registry :: get()->config->database->params->tblprefixbk)) {
			throw new Exception(Aitsu_Translate :: translate('No backup table prefix specified.'));
		}

		try {
			$tables = Aitsu_Db :: fetchCol('' .
			'show tables like ?', array (
				Aitsu_Registry :: get()->config->database->params->tblprefix . '%'
			));
		} catch (Exception $e) {
			throw new Exception(Aitsu_Translate :: translate('Show tables privilege is missing or database access denied.'));
		}

		if (!$tables) {
			throw new Exception(Aitsu_Translate :: translate('There seem to be no tables to be backuped'));
		}

		$this->_tables = $tables;

		if ($this->_currentStep >= count($tables)) {
			return false;
		}

		return true;
	}

	protected function _next() {
		
		if (!isset($this->_tables[$this->_currentStep + 1])) {
			return '';
		}

		return sprintf(Aitsu_Translate :: translate('Copying table %s.'), $this->_tables[$this->_currentStep + 1]);
	}

	protected function _executeStep() {

		$table = $this->_tables[$this->_currentStep];

		$bkTable = substr($table, strlen(Aitsu_Registry :: get()->config->database->params->tblprefix));
		$bkTable = Aitsu_Registry :: get()->config->database->params->tblprefixbk . $bkTable;
		Aitsu_Db :: query('create table ' . $bkTable . ' select * from ' . $table, null, true);

		$response = sprintf(Aitsu_Translate :: translate('Table %s copied.'), $this->_tables[$this->_currentStep]);
		return Aitsu_Adm_Script_Response :: factory($response);
	}

}