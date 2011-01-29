<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_String {

	public function __construct() {
	}

	public static function shorten($text, $length) {

		$text = explode('{BREAK}', wordwrap($text, $length, "{BREAK}"));
		
		return $text[0];
	}

}