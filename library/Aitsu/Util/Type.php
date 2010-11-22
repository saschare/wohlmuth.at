<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Type.php 19423 2010-10-20 13:06:08Z akm $}
 */

class Aitsu_Util_Type {

	public static function number($number) {
		
		return (float) preg_replace('/[^\\d]*/', '', $number);
	}
	
}