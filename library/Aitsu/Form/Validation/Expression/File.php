<?php


/**
 * File.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: File.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Form_Validation_Expression_File implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;

		if (!isset ($this->args['fieldName'])) {
			throw new Aitsu_Form_Validation_Expression_Exception('Fieldname must not be empty.');
		}
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {

		$fileName = $this->args['fieldName'];
		
		if (!isset($_FILES[$fileName]) || $_FILES[$fileName]['size'] == 0) {
			return (isset ($this->args['required']) && $this->args['required'] === true) ? false : true;
		}

		if (isset($this->args['maxSize']) && $_FILES[$fileName]['size'] > $this->args['maxSize']) {
			return false;
		}
		
		if (isset($this->args['type']) && !empty($_FILES[$fileName]['type']) && $_FILES[$fileName]['type'] != $this->args['type']) {
			return false;
		}
		
		$pathInfo = pathinfo($_FILES[$fileName]['name']);
		if (isset($this->args['extension']) && strtolower($this->args['extension']) != strtolower($pathInfo['extension'])) {
			return false;
		}

		return true;
	}
}