<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_Request {

	public static function header($header) {

		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if (isset ($_SERVER[$temp])) {
			return $_SERVER[$temp];
		}

		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (isset ($headers[$header])) {
				return $headers[$header];
			}
			$header = strtolower($header);
			foreach ($headers as $key => $value) {
				if (strtolower($key) == $header) {
					return $value;
				}
			}
		}

		return false;
	}

}