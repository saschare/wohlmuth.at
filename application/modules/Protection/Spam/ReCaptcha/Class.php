<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */

class Module_Protection_Spam_ReCaptcha_Class extends Aitsu_Module_Abstract {

	protected $_allowEdit = true;

	protected function _init() {

		$this->_verifyResponse();
		
		if (Aitsu_User_Status :: isHuman()) {
			/*
			 * We have to redirect the browser to where the user has
			 * been before.
			 */
			header('Location: ' . Aitsu_User_Status :: getUrl(1));
			exit;
		}

		$view = $this->_getView();

		$theme = Aitsu_Content_Config_Radio :: set($this->_index, 'ReCaptcha.theme', '', array (
			'Rot (default)' => 'red',
			'Weiss' => 'white',
			'Schwarz' => 'blackglass',
			'Clean' => 'clean',
			'Custom' => 'custom'
		), 'Theme');

		$template = $theme == 'custom' ? 'custom' : 'index';
		$view->theme = empty ($theme) ? 'red' : $theme;

		if (Aitsu_Application_Status :: isEdit()) {
			return;
		}

		$return = $view->render('init.phtml');
		$return .= $view->render($template . '.phtml');

		return $return;
	}

	protected function _verifyResponse() {

		if (empty ($_POST) || empty ($_POST['recaptcha_challenge_field']) || empty ($_POST['recaptcha_response_field'])) {
			return;
		}

		$client = new Zend_Http_Client('http://www.google.com/recaptcha/api/verify', array (
			'maxredirects' => 5,
			'timeout' => 30
		));
		$client->setParameterPost(array (
			'privatekey' => Aitsu_Config :: get('captcha.recaptcha.key.private'),
			'remoteip' => $_SERVER['REMOTE_ADDR'],
			'challenge' => $_POST['recaptcha_challenge_field'],
			'response' => $_POST['recaptcha_response_field']
		));
		$response = $client->request('POST');
		
		if (substr($response->getBody(), 0, 4) == 'true') {
			Aitsu_User_Status :: isHuman(true);
		}
	}
}