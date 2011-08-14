<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Form_Validation_Expression_Enum implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {

		if (in_array($value, $this->args)) {
			return true;
		}

		$value = null;

		return false;
	}
}