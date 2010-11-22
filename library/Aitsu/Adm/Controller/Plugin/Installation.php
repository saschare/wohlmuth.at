<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Accesscontrol.php 18769 2010-09-14 19:02:01Z akm $}
 */

class Aitsu_Adm_Controller_Plugin_Installation extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if ($request->getControllerName() != 'script') {
			$request->setControllerName('script');
			$request->setActionName('installation');
			
			if ($request->isXmlHttpRequest()) {
				$request->setParam('exec', 'Adm_Script_Setup');
			} else {
				$request->setParam('show', 'Adm_Script_Setup');
			}
			
			$request->setDispatched(false);
		}
	}
}