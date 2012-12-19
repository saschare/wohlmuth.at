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
			if (!isset ($config-> $parts[$i])) {
				return false;
			}
			$config = $config-> $parts[$i];
		}

		return $config == $value;
	}

	/**
	 * @var String The configuration string to get the value from.
	 * @var Boolean Set this value to true to bypass the caching. Default is false.
	 * @return Mixed The value set in the configuration for the specified string.
	 */
	public static function get($string, $fresh = false) {

		static $val = array ();

		if (!$fresh && isset ($val[$string])) {
			return $val[$string];
		}

		$config = Aitsu_Registry :: get()->config;
		$parts = explode('.', $string);

		for ($i = 0; $i < count($parts); $i++) {
			if (!isset ($config-> $parts[$i])) {
				return false;
			}
			$config = $config-> $parts[$i];
		}

		$val[$string] = $config;

		return $config;
	}

}