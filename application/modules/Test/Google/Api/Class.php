<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Test_Google_Api_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$client = Google_Api :: factory('apiClient');
		$client->setClientId('634362671258.apps.googleusercontent.com');
		$client->setClientSecret('aX7S7P4bQKzG_j6qK3tqpbnX');
		$client->setRedirectUri('http://dev.aitsu.ch/oauth2callback');
		$client->setApplicationName('aitsu MicroApp');
		$client->setScopes('https://www.google.com/calendar/feeds/');
		$client->setState($_SERVER['REQUEST_URI']);
		$client->setApprovalPrompt('auto');
		
		if (empty(Aitsu_Registry :: get()->session->google->api->accessToken)) {
			Aitsu_Registry :: get()->session->google->api->accessToken = $client->authenticate();
		} else {
			$client->setAccessToken(Aitsu_Registry :: get()->session->google->api->accessToken);
		}
		
		return var_export($client->getAccessToken(), true);
	}

}