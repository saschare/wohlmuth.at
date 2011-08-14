<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_Controller_Plugin_Accesscontrol extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if ($request->getParam('login') != null && $request->getParam('password') != null) {
			
			if (strtolower($request->getParam('login')) == 'setup' && $request->getParam('password') == Aitsu_Registry :: get()->config->setup->password) {
				try {
					Aitsu_Db :: fetchOne('select count(*) from _art');
					/*
					 * As no exception has occured, the installation is probably
					 * already made. This information should, however, not be visible 
					 * to the visitor.
					 */
					header('Location: ./');
					exit;
				} catch (Exception $e) {					
					Aitsu_Registry :: get()->session->user = Aitsu_Adm_User :: setupLogin();					
					$request->setControllerName('setup')->setActionName('index')->setDispatched(true);					
					return;
				}
			}
			
			try {
				$user = Aitsu_Persistence_View_User :: auth($request->getParam('login'), $request->getParam('password'));
			} catch (Exception $e) {
				/*
				 * Probably no installation made yet. This information should, however,
				 * not be visible to the visitor.
				 */
				header('Location: ./');
				exit;
			}
			
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
			if (Aitsu_Adm_User :: getInstance()->getId() == 'setup' && $request->getControllerName() == 'index') {
				Zend_Session :: destroy();
				header('Location: ./');
				exit;
			}
		}
		elseif ($request->getActionName() != 'login' || $request->getControllerName() != 'acl') {
			$request->setControllerName('acl')->setActionName('login')->setDispatched(false);
			return;
		}
	}
}