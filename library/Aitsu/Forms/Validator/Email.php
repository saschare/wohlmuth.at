<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms_Validator_Email extends Aitsu_Forms_Validator {

	protected function __construct() {
	}

	public static function factory($args) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function _isValid($value) {

		if (!preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,5}/i', $value)) {
			$this->_message = Aitsu_Translate :: translate('The value does not represent an email.');
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