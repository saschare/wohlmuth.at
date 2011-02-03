<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_Controller_Plugin_HmacAuth extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$auth = $request->getHeader('Authorization');

		if (!$auth)
			return;

		if (!preg_match('/([^\\s]*)\\s([^\\:]*)\\:(.*)/', $auth, $match))
			return;

		$auth = array (
			'type' => $match[1],
			'userid' => $match[2],
			'hash' => $match[3]
		);

		$uri = $_SERVER['REQUEST_URI'];
		$body = $request->getRawBody();

		$secret = Aitsu_Db :: fetchOne('' .
		'select password from _acl_user where login = :userid', array (
			':userid' => $auth['userid']
		));
		
		$checkHash = hash_hmac ('sha1', $uri . $body, $secret);

		if ($auth['hash'] != $checkHash) {
			header('HTTP/1.1 401 Access Denied');
			exit;
		}
		
		Aitsu_Adm_User :: login($auth['userid'], $secret, true);
		Aitsu_Registry :: get()->session->user = Aitsu_Adm_User :: getInstance();
	}
}