<?php


/**
 * aitsu Form Validation Expression Interface.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Interface.php 15620 2010-03-26 14:42:09Z akm $}
 */

interface Aitsu_Form_Validation_Expression_Interface {
	
	public static function init($args);
	
	public function isValid(& $value);
	
}