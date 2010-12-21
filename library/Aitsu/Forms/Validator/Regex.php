<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms_Validator_Regex extends Aitsu_Forms_Validator {
	
	protected function __construct($regex, $args) {
		
		$this->_args = (object) $args[1];
		$this->_regex = $args[0];
	}

	public static function factory() {
		
		static $instance = array();
		static $id = null;
		
		$args = func_get_args();
		$regex = $args[0];
		if (count($args) > 1) {
			$args = $args[1];
		}
		
		if ($args != null) {
			$id = md5(serialize($args));
		}
		
		if (isset($instance[$id])) {
			$instance[$id] = new self($regex, $args);
		}
		
		return $instance[$id];
	}

	public function _isValid($value) {
		
		if (!preg_match($this->_regex, $value)) {
			$this->_message = Aitsu_Translate :: translate('The value does not match the given pattern');
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