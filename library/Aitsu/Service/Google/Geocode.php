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

	/**
	 * Constructor.
	 * 
	 * @param Boolean True, to use SSL encryption. False otherwise. Defaults to false.
	 * @return Aitsu_Service_Google_Geocode An instance of the class.
	 */
	public function __construct($useSSL = false) {

		$this->_serviceUrl = ($useSSL ? 'https://' : 'http://') . self :: SERVICE_URL;
	}

	/**
	 * Singleton accessor.
	 * 
	 * @param Boolean True, to use SSL encryption. False otherwise. Defaults to false.
	 * @return Aitsu_Service_Google_Geocode An instance of the class.
	 */
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

	/**
	 * Locates the requested address and persists the data in ait_google_geolocation.
	 * 
	 * @param String Address to be located.
	 * @param Integer The maximum acceptable age (in seconds) of the result. If the result is
	 * older, the request is redirected to Google's Geolocation API. To overrule the cache, use
	 * a value of 0. Default is one year.
	 * @return Object Standard object containing the address id, lat and lng.
	 */
	public function locate($address, $overruleCacheIfResultIsOlderThan = 31536000) {

		$hash = hash('md4', $address);

		$location = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	id, lat, lng ' .
		'from _google_geolocation ' .
		'where ' .
		'	hash = :hash ' .
		'	and date_add(requested, interval :age second) > now()', array (
			':hash' => $hash,
			':age' => $overruleCacheIfResultIsOlderThan
		));

		if ($location) {
			return (object) $location;
		}

		try {
			$client = new Zend_Http_Client($this->_serviceUrl, array (
				'maxredirects' => 0,
				'timeout' => 10
			));

			$client->setParameterGet(array (
				'sensor' => 'false',
				'address' => $address
			));

			$response = $client->request()->getBody();
			$locator = Zend_Json :: decode($response);
		} catch (Exception $e) {
			/*
			 * Just prevent the exception from being thrown.
			 */
		}

		$lat = isset ($locator['results'][0]['geometry']['location']['lat']) ? $locator['results'][0]['geometry']['location']['lat'] : null;
		$lng = isset ($locator['results'][0]['geometry']['location']['lng']) ? $locator['results'][0]['geometry']['location']['lng'] : null;

		try {
			$id = Aitsu_Db :: query('' .
			'insert into _google_geolocation ' .
			'(address, hash, lat, lng, jsonresponse) ' .
			'values ' .
			'(:address, :hash, :lat, :lng, :jsonresponse)', array (
				':address' => $address,
				':hash' => $hash,
				':lat' => $lat,
				':lng' => $lng,
				':jsonresponse' => $response
			))->getLastInsertId();
		} catch (Exception $e) {
			$id = null;
		}

		return (object) array (
			'id' => $id,
			'lat' => $lat,
			'lng' => $lng
		);
	}
}
