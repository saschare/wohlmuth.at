<?php


/**
 * Google Plus API implementation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 * 
 * @link https://developers.google.com/+/api/ Google+ API Documentation
 */
class Module_Google_Plus_Class extends Aitsu_Module_Abstract {
	
	protected $_cacheIfLoggedIn = true;
	protected $_disableCacheArticleRelation = true;

	protected $_apiKey = null;
	protected $_userId = null;
	protected $_endPoint = null;

	protected function _init() {

		$this->_apiKey = Aitsu_Config :: get('google.api.key');
		$this->_userId = Aitsu_Content_Config_Text :: set($this->_index, 'Google.Plus.UserId', 'User ID', 'Google+ User ID');		
		$this->_endPoint = 'https://www.googleapis.com/plus/v1/people/' . $this->_userId . '/activities/public';
		
		$this->_id = $this->_endPoint;
	}

	protected function _main() {
		
		if (empty($this->_userId)) {
			return '';
		}

		$client = new Zend_Http_Client($this->_endPoint, array (
			'maxredirects' => 0,
			'timeout' => 30
		));
		$client->setParameterGet(array (
			'key' => $this->_apiKey,
			'maxResults' => 25
		));
		$response = $client->request();
		
		$view = $this->_getView();
		$view->data = json_decode($response->getBody());
		
		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {
		/*
		 * 1 hour.
		 */
		return 3600;
	}

}