<?php


/**
 * @author Christian Kehres, webtischlerei.de
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Config_Ini {

	protected function __construct() {
	}

	public static function getInstance($ini) {

		if (!empty ($_SERVER['PHP_FCGI_CHILDREN']) || !empty ($_SERVER['FCGI_ROLE'])) {
			$env = (getenv("REDIRECT_AITSU_ENV") == '' ? 'default' : getenv("REDIRECT_AITSU_ENV"));
		} else {
			$env = (getenv("AITSU_ENV") == '' ? 'default' : getenv("AITSU_ENV"));
		}

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