<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 * 
 * @link http://code.google.com/apis/maps/documentation/geocoding/ Google Geocoding Documentation
 * 
 * Please read and follow the usage limits set by Google within above mentioned document. At
 * the time of class development there are mainly two usage limits:
 * 
 * - Usage is limited to 2'500 requests per 24 hours from a certain installation or IP address,
 * - The result has to be used within a Google Map. However, requesting the result and caching
 *   the result locally is explicitly allowed.
 * 
 * The class requests the provided address and persists the result using the provided address
 * as the key candidate. To make a relation to e.g. an article, store the address in the article
 * and reference the the geocode to the address stored in ait_google_geolocation.
 */
class Aitsu_Service_Google_Geocode {

	const SERVICE_URL = 'maps.googleapis.com/maps/api/geocode/json';

	protected $_serviceUrl = null;

	public function __construct($useSSL = false) {

		$this->_serviceUrl = ($useSSL ? 'https://' : 'http://') . self :: SERVICE_URL;
	}

	public static function getInstance($useSSL = false) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self($useSSL);
		}
		elseif ($useSSL) {
			$instance->_serviceUrl = 'https://' . self :: SERVICE_URL;
		}

		return $instance;
	}

	public function locate($address, $overruleCacheIfResultIsOlderThan = 31536000) {

		$hash = hash('md4', $address);

		$location = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	lat, lng ' .
		'from _google_geolocation ' .
		'where ' .
		'	hash = :hash ' .
		'	and date_add(requested, interval :age second) < now()', array (
			':hash' => $hash,
			':age' => $overruleCacheIfResultIsOlderThan
		));

		if ($location) {
			return (object) $location;
		}
		
		
	}
}
?>
