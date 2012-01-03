<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Google_Api {

	private function __construct() {
	}

	public static function factory($apiPath, $apiClient = null, $apiConfig = null) {
		
		Aitsu_Registry :: get()->session->oAuthRequestingUrl = $_SERVER['REQUEST_URI'];

		$apiPath = realpath(APPLICATION_PATH . '/../library/Google/' . $apiPath . '.php');
		
		require_once ($apiPath);

		$path = pathinfo($apiPath);

		return new $path['filename'];
	}
	
	public static function getUrl($url = null) {
		
		return Aitsu_Registry :: get()->session->oAuthRequestingUrl;
	}
	
}