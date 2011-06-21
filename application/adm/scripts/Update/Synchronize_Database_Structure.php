<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Adm_Script_Synchronize_Database_Structure extends Aitsu_Adm_Script_Abstract {

	protected $_methodMap = array ();

	public static function getName() {

		return Aitsu_Translate :: translate('Synchronize database structure');
	}

	protected function _init() {

		$this->_methodMap = array (
			0 => '_removeConstraints',
			1 => '_removeIndexes',
			2 => '_removeViews',
			3 => '_removeEmptyTables',
			4 => '_restoreTables',
			100000 => '_restoreIndexes',
			100001 => '_restoreConstraints',
			100002 => '_restoreViews'
		);
	}

	protected function _hasNext() {

		if ($this->_currentStep < 8) {
			return true;
		}

		return false;
	}

	protected function _next() {

		return 'Next line to be executed.';
	}

	protected function _executeStep() {

		$method = $this->_methodMap[$this->_currentStep];
		$response = call_user_func_array(array (
			$this,
			$method
		), array ());

		return Aitsu_Adm_Script_Response :: factory($response);
	}

	protected function _removeConstraints() {
		
		return 'constraints removed';
	}

	protected function _removeIndexes() {
		
		return 'indexes removed';
	}

	protected function _removeViews() {
		
		return 'views removed';
	}

	protected function _removeEmptyTables() {
		
		return 'empty tables removed';
	}

	protected function _restoreTables() {

		$this->_currentStep = 100000 - 1;
		
		return 'tables restored';
	}

	protected function _restoreIndexes() {
		
		return 'indexes restored';
	}

	protected function _restoreConstraints() {
		
		return 'constraints restored';
	}

	protected function _restoreViews() {
		
		return 'views restored';
	}

}