<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Error {

	public static function add($code) {

		Aitsu_Service_Google_Analytics :: noPageView();
		Aitsu_Service_Google_Analytics_Event :: add('Error', $code, new Aitsu_Service_Google_Analytics_Javascript('page: \' + document.location.pathname + document.location.search + \' ref: \' + document.referrer'), 1, true);
	}

}