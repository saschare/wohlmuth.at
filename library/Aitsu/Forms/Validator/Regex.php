<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms_Validator_Regex extends Aitsu_Forms_Validator {
	
	protected function __construct($args) {
		
		$this->_regex = isset($args['regex']) ? $args['regex'] : '/.*/';
		unset($args['regex']);
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
		
		if (!preg_match($this->_regex, $value)) {
			$this->_message = Aitsu_Translate :: translate('The value does not match the given pattern.');
			return false;
		}
		
		if (isset($this->_args->min) && strlen($value) < $this->_args->min) {
			$this->_message = sprintf(Aitsu_Translate :: translate('The value must be at least %u characters long.'), $this->_args->min);
			return false;
		}

		if (isset($this->_args->max) && strlen($value) > $this->_args->max) {
			$this->_message = sprintf(Aitsu_Translate :: translate('The value must not be more than %u characters long.'), $this->_args->max);
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