<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Controller.php 19988 2010-11-19 16:11:32Z akm $}
 */

abstract class Aitsu_Adm_Plugin_Controller extends Zend_Controller_Action {

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array ()) {

		/*
		 * The constructor is not called, because it subsequently calls
		 * the init method.
		 */
		// parent :: __construct($request, $response, $invokeArgs);

		$this->setRequest($request)->setResponse($response)->_setInvokeArgs($invokeArgs);
		$this->_helper = new Zend_Controller_Action_HelperBroker($this);

		$this->_preInit();

		$this->_helper->viewRenderer->setNoController();

		$this->init();
	}

	protected function _preInit() {

	}

	public static function getPosition($idart, $plugin) {
		
		if (!isset(Aitsu_Article_Config :: factory($idart)->plugin->article-> $plugin->position)) {
			return 0;
		}

		$position = Aitsu_Article_Config :: factory($idart)->plugin->article-> $plugin->position;

		if (!isset ($position->ifindex)) {
			return $position->default;
		}

		if (Aitsu_Persistence_Article :: factory($idart)->isIndex()) {
			return $position->ifindex;
		}

		return $position->default;
	}
}