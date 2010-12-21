<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

abstract class Aitsu_Forms_Validator {
	
	protected $_args;
	protected $_message = null;
	
	abstract public static function factory();
	
	public function isValid($value) {
		
		if (isset($this->_args->required) && $this->_args->required && empty($value)) {
			$this->_message = Aitsu_Translate :: translate('The submission of a value is mandatory.');
			return false;
		}
		
		if ((!isset($this->_args->required) || !$this->_args->required) && empty($value)) {
			return true;
		}
		
		return $this->_isValid($value);
	}

	abstract protected function _isValid($value);

	abstract public function getMessage();

}