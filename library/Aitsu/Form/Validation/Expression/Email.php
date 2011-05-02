<?php


/**
 * Email.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Email.php 15621 2010-03-26 14:43:21Z akm $}
 */

class Aitsu_Form_Validation_Expression_Email implements Aitsu_Form_Validation_Expression_Interface {
	
	protected function __construct() {
	}
	
	public static function init($args) {
		
		return new self();
	}
	
	public function isValid(& $value) {
		
		return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i', $value);
	}
}