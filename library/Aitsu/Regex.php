<?php


/**
 * Aitsu regex utitlities.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, kummer
 * 
 * {@id $Id: Regex.php 16578 2010-05-26 07:22:47Z akm $}
 */

class Aitsu_Regex {
	
	public static function getFirstDecimalBlock($string) {
		
		if (!preg_match('/\\d{1,}/', $string, $match)) {
			return null;
		}
		
		return $match[0];
	}
}