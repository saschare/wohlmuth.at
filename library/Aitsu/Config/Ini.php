<?php


/**
 * @author Christian Kehres, webtischlerei.de
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, webtischlerei.de
 */

class Aitsu_Config_Ini {

	protected function __construct() {
	}

	public static function getInstance($ini) {

		$env = (getenv("AITSU_ENV") == '' ? 'live' : getenv("AITSU_ENV"));

		$config = new Zend_Config_Ini('application/configs/config.ini', $env, array (
			'allowModifications' => true
		));

		if ($ini != 'backend') {
			$client_config = new Zend_Config_Ini('application/configs/' . $ini . '.ini', $env, array (
				'allowModifications' => true
			));

			$config->merge($client_config);
		}
		
		return $config;
	}
}