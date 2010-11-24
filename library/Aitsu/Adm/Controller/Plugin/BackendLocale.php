<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_Controller_Plugin_BackendLocale extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$lang = 'en';
		if (Aitsu_Adm_User :: getInstance() != null) {
			$lang = substr(Aitsu_Adm_User :: getInstance()->locale, 0, 2);
			trigger_error('Locale: ' . Aitsu_Adm_User :: getInstance()->locale);
		} else {
			trigger_error('Aitsu_Adm_User is null!');
		}

		$lang = in_array($lang, array (
			'en',
			'de'
		)) ? $lang : 'en';

		$adapter = new Zend_Translate('gettext', APPLICATION_PATH . '/languages/' . $lang . '/translate.mo', $lang);
		Aitsu_Registry :: get()->Zend_Translate = $adapter;
		Zend_Registry :: set('Zend_Translate', $adapter);

		Aitsu_Registry :: get()->env->locale = new Zend_Locale($lang);
	}
}