<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Adm_Script_Synchronize_Database_Structure extends Aitsu_Adm_Script_Abstract {

	protected $_lines = array ();

	public static function getName() {
		
		return Aitsu_Translate :: translate('Synchronize database structure');
	}
	
	protected function _init() {
		
		
	}

	protected function _hasNext() {

		if ($this->_currentStep < 20) {
			return true;
		}
		
		return false;
	}

	protected function _next() {

		return 'Next line to be executed.';
	}

	protected function _executeStep() {

		$response = 'Test ' . $this->_currentStep;
		return Aitsu_Adm_Script_Response :: factory($response);
	}

}