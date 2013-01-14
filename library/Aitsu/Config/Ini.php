<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 * 
 * @todo Vgl. Kommentarblock vom 12.01.13
 */
class Aitsu_Config_Ini {

	protected function __construct() {
	}

	public static function getInstance($ini) {

		/*
		 * 12.01.13, A. Kummer: Der folgende Block muss überarbeitet werden. Liegen die
		 * bezeichneten Servervariablen vor, wird die Einstellung durch die Umgebungsvariable
		 * AITSU_ENV ignoriert. Das bedetuet, dass derselbe einfach an zwei Umgebungsvariablen
		 * übergeben werden muss.
		 */
		if (!empty ($_SERVER['PHP_FCGI_CHILDREN']) || !empty ($_SERVER['FCGI_ROLE'])) {
			$env = (getenv("REDIRECT_AITSU_ENV") == '' ? 'live' : getenv("REDIRECT_AITSU_ENV"));
		} else {
			$env = (getenv("AITSU_ENV") == '' ? 'live' : getenv("AITSU_ENV"));
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