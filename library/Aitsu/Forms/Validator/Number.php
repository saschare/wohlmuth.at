<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms_Validator_Number extends Aitsu_Forms_Validator {
	
	protected function __construct($args) {
		
		$this->_args = (object) $args;
	}

	public static function factory($args) {
		
		static $instance = array();
		static $id = null;
		
		if ($args != null) {
			$id = md5(serialize($args));
		}
		
		if (!isset($instance[$id])) {
			$instance[$id] = new self($args);
		}
		
		return $instance[$id];
	}

	public function _isValid($value) {
		
		if (!is_numeric($value)) {
			$this->_message = Aitsu_Translate :: translate('The value is not numeric.');
			return false;
		}
		
		if (isset($this->_args->max) && $value > $this->_args->max) {
			$this->_message = sprintf(Aitsu_Translate :: translate('The value must be lower or equal than %F.'), $this->_args->max);
			return false;
		}

		if (isset($this->_args->min) && $value < $this->_args->min) {
			$this->_message = sprintf(Aitsu_Translate :: translate('The value must be greater or equal than %F.'), $this->_args->min);
			return false;
		}

		return true;
	}

	public function getMessage() {
		
		if ($this->_message == null) {
			return '';
		}
		
		return $this->_message;
	}

}