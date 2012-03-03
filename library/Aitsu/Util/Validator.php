<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Util_Validator {
	
	/**
	 * Checks the given string against a simple but fast regular expression. Be aware
	 * that the regex does not check validity according to RFC 5322.
	 * @var String The email address to be checked. Trim the value, if it contains leading 
	 * or trailing whitespace.
	 * @return Boolean True, if it matches. False otherwise.
	 */
	public static function isEmail($string) {
		
		return preg_match('/\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $string) > 0;
	}
	
	/**
	 * Checks the given string to contain whitespace and numbers only. A typical application
	 * is to check an input to be a possible phone or fax number. The validator, however, does
	 * not check for a particular length or the count of the numbers.
	 * @var String The string to be checked.
	 * @return Boolean True, if it matches. False otherwise.
	 */
	public static function hasNumbersAndWhitespaceOnly($string) {
		
		return preg_match('/^[0-9\\s]*$/', $string) > 0;
	}
}