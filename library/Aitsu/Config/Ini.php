<?php


/**
 * Aitsu Config Ini
 *
 * @author Christian Kehres, webtischlerei.de
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, webtischlerei.de
 *
 * {@id $Id: Ini.php 17778 2010-07-27 17:44:55Z akm $}
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
		
		if (substr_count ($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') == 0) {
			Aitsu_Registry :: get()->config->output->gzhandler = false;
		}

		return $config;
	}
}