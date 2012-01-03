<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Google_Api {

	private static function __construct() {
	}

	public static function factory($apiPath, $apiConfig = null) {

		require_once ($apiPath);

		$path = pathinfo($apiPath);

		return new $path['filename'];
	}
}