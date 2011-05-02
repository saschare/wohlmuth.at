<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Form_Validation_Expression_Depending implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {
		
		if (!isset($_POST[$this->args['on']]) || empty($_POST[$this->args['on']])) {
			return true;
		}
		
		if (!empty($value)) {
			return true;
		}
		
		return false;
	}
}