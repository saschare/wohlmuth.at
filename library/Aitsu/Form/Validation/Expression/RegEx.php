<?php


/**
 * RegEx.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: RegEx.php 15615 2010-03-26 09:39:09Z akm $}
 */

class Aitsu_Form_Validation_Expression_RegEx implements Aitsu_Form_Validation_Expression_Interface {
	
	protected $args;
	
	protected function __construct($args) {
		
		$this->args = $args;
	}
	
	public static function init($args) {
		
		return new self($args);
	}
	
	public function isValid(& $value) {
		
		return preg_match($this->args['regex'], $value);
	}
}