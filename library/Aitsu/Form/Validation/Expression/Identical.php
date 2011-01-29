<?php


/**
 * @author Conrad Leu, mereo GmbH
 * @copyright Copyright &copy; 2011, mereo GmbH
 */

/**
 * This class has been provided by Conrad Leu as a contribution
 * to aitsu. Its fitness for the intended purpose has not yet 
 * been proved by the development team.
 */
class Aitsu_Form_Validation_Expression_Identical implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {

		if (isset ($this->args['identical']) && $_POST[$this->args['identical']] == $value) {
			return true;
		}
		return false;
	}
}