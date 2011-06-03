<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_Controller_Plugin_Clientlang extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		if (Aitsu_Adm_User :: getInstance() == null || Aitsu_Adm_User :: getInstance()->getId() == 'setup') {
			return;
		}

		$prefLang = Aitsu_Adm_User :: getInstance()->idlang;

		if (empty (Aitsu_Registry :: get()->session->currentClient)) {
			Aitsu_Registry :: get()->session->currentClient = Aitsu_Db :: fetchOne('' .
			'select idclient from _lang where idlang = :idlang', array (
				':idlang' => $prefLang
			));
		}

		if (empty (Aitsu_Registry :: get()->session->currentLanguage)) {
			/*
			 * First access, no session established yet.
			 */
			Aitsu_Registry :: get()->session->currentLanguage = $prefLang;
		}

		Aitsu_Registry :: get()->env->idlang = Aitsu_Registry :: get()->session->currentLanguage;
	}
}