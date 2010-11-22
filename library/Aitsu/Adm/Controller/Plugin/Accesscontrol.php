<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Accesscontrol.php 18769 2010-09-14 19:02:01Z akm $}
 */

class Aitsu_Adm_Controller_Plugin_Accesscontrol extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if ($request->getParam('login') != null && $request->getParam('password') != null) {
			$user = Aitsu_Persistence_View_User :: auth($request->getParam('login'), $request->getParam('password'));
			if ($user !== false) {
				Aitsu_Registry :: get()->session->sessionPeriod = 600;
				
				Aitsu_Adm_User :: login($request->getParam('login'), $request->getParam('password'));
				Aitsu_Registry :: get()->session->user = Aitsu_Adm_User :: getInstance();
				
				$sessionPeriod = Aitsu_Adm_User :: getInstance()->sessionperiod;				
				if ($sessionPeriod >= 300 && $sessionPeriod <= 14400) {
					Aitsu_Registry :: get()->session->sessionPeriod = $sessionPeriod;
				}
			}
		}

		if (isset (Aitsu_Registry :: get()->session->user)) {
			Aitsu_Adm_User :: rehydrate(Aitsu_Registry :: get()->session->user);
		}
		elseif ($request->getActionName() != 'login' || $request->getControllerName() != 'acl') {
			$request->setControllerName('acl')->setActionName('login')->setDispatched(false);
			return;
		}
	}
}