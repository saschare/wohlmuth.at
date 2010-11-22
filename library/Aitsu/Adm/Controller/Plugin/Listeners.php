<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Listeners.php 19472 2010-10-21 13:45:56Z akm $}
 */

class Aitsu_Adm_Controller_Plugin_Listeners extends Zend_Controller_Plugin_Abstract {

	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		Aitsu_Event_BackendRequest :: raise('preDispatch', $request);
	}

	public function postDispatch(Zend_Controller_Request_Abstract $request) {

		Aitsu_Event_BackendRequest :: raise('postDispatch', $request);
	}

}