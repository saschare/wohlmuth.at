<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_Type {

	public static function number($number) {
		
		return (float) preg_replace('/[^\\d]*/', '', $number);
	}
	
	public static function integer($arg) {
		
		return (boolean) (string) $arg == (int) $arg;
	}
	
}