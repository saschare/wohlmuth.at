<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Adm_Script_Abstract {

	protected $_methods = array ();
	protected $_currentStep;

	public function __construct($step) {
		
		$this->_init();

		$methods = get_class_methods($this);

		foreach ($methods as $method) {
			if (substr($method, 0, 2) == 'do') {
				$this->_methods[] = $method;
			}
		}

		$this->_currentStep = $step -1;
	}
	
	protected function _init() {
		
		/*
		 * Overwrite this method to do initialization of
		 * the script if necessary.
		 */
	}

	public static function getName() {
		
		return;
	}

	public function exec() {

		try {

			if (!$this->_hasNext()) {
				return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Script end reached'), null, null, true);
			}

			$response = $this->_executeStep();

			$response->setNextStep($this->_next());

			return $response;

		} catch (Exception $e) {
			return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Uncaught exception'), 'failure', $e, true);
		}
	}

	protected function _hasMethods() {

		if (count($this->_methods) == 0) {
			return false;
		}

		return true;
	}

	/*
	 * Overwrite this method accordingly, if the script does not 
	 * have a known number of methods to be exectued. The method 
	 * returns true, if there is another method to be executed or
	 * false otherwise.
	 */
	protected function _hasNext() {

		if ($this->_currentStep >= count($this->_methods)) {
			return false;
		}

		return true;
	}

	/*
	 * Overwrite this method accordingly, if the script does not
	 * have a known number of methods to be executed. The method
	 * returns a string containing information about the next step
	 * to be displayed in the user's console.
	 */
	protected function _next() {
		
		if (!isset($this->_methods[$this->_currentStep + 1])) {
			return '';
		}

		return substr($this->_methods[$this->_currentStep + 1], 2);
	}

	/*
	 * Overwrite this method accordingly, if the script does not
	 * have a known number of methods to be executed. The method
	 * executes the step and returns the return of the executed 
	 * method, which must be of type Aitsu_Adm_Script_Response.
	 */
	protected function _executeStep() {

		return $this-> {
			$this->_methods[$this->_currentStep] }
		();
	}
}