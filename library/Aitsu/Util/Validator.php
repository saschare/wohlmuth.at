<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Util_Validator {
	
	public static function isEmail($string) {
		
		return preg_match('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $string) > 0;
	}
	
	public static function hasNumbersAndWhitespaceOnly($string) {
		
		return preg_match('/^[0-9\\s]*$/', $string) > 0;
	}
}