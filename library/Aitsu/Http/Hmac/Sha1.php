<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Http_Hmac_Sha1 {

	protected $_target;
	protected $_userid;
	protected $_secret;
	protected $_params = array ();
	protected $_method = 'GET';

	protected function __construct() {
	}

	public static function factory($target, $userid, $secret) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		$instance->_target = $target;
		$instance->_userid = $userid;
		$instance->_secret = $secret;
		$instance->_params = array ();

		return $instance;
	}

	public function setMethod($method) {

		$this->_method = $method;

		return $this;
	}

	public function addParam($key, $value) {

		$this->_params[$key] = $value;

		return $this;
	}

	public function setParams($params) {

		$this->_params = $params;

		return $this;
	}

	public function getResponse() {
		
		$query = http_build_query($this->_params);
		$uriPart = preg_replace('|[^/]*\\:/{2}[^/]*|', '', $this->_target);

		$client = new Zend_Http_Client($this->_target, array (
			'maxredirects' => 0,
			'timeout' => 10
		));

		if ($this->_method == 'GET') {
			$client->setUri($this->_target . '?' . $query);
			$client->setHeaders('aitsuauth', 'HMAC-SHA1 ' . $this->_userid . ':' . hash_hmac('SHA1', $uriPart . '?' . $query, $this->_secret));
		} else {
			$client->setRawData($query);
			$client->setHeaders('aitsuauth', 'HMAC-SHA1 ' . $this->_userid . ':' . hash_hmac('SHA1', $uriPart . $query, $this->_secret));
		}

		try {
			return $client->request($this->_method)->getBody();
		} catch (Exception $e) {
			return false;
		}
	}
}