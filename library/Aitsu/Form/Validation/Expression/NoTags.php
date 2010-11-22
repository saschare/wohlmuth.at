<?php


/**
 * No HTML- or PHP- or JS-Code allowed.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: NoTags.php 15613 2010-03-26 09:26:19Z akm $}
 */

class Aitsu_Form_Validation_Expression_NoTags implements Aitsu_Form_Validation_Expression_Interface {
	
	protected $args;
	
	protected function __construct($args) {
		
		$this->args = $args;
	}
	
	public static function init($args) {
		
		return new self($args);
	}
	
	public function isValid(& $value) {
		
		if (isset($this->args['maxlength']) && strlen($value) > $this->args['maxlength']) {
			return false;
		}
		
		return strlen(trim($value)) == strlen(strip_tags(trim($value)));
	}
}