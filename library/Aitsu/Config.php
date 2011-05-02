<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Config {

	public static function equals($string, $value) {

		static $val = array ();

		if (isset ($val[$string])) {
			return $val[$string] == $value;
		}

		$config = Aitsu_Registry :: get()->config;
		$parts = explode('.', $string);

		for ($i = 0; $i < count($parts); $i++) {
			if (!isset($config-> $parts[$i])) {
				return false;
			}
			$config = $config-> $parts[$i];
		}
		
		return $config == $value;
	}
	
	public static function get($string) {
		
		static $val = array ();

		if (isset ($val[$string])) {
			return $val[$string];
		}

		$config = Aitsu_Registry :: get()->config;
		$parts = explode('.', $string);

		for ($i = 0; $i < count($parts); $i++) {
			if (!isset($config-> $parts[$i])) {
				return false;
			}
			$config = $config-> $parts[$i];
		}
		
		$val = $config;
		
		return $config;
	}

}