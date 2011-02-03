<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RestController extends Zend_Controller_Action {

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public function indexAction() {

		$api = $this->getRequest()->getParam('api');
		$method = $this->getRequest()->getParam('method');

		$this->_helper->viewRenderer->setNoRender(true);

		include_once (APPLICATION_PATH . '/rest/' . $api . '/Class.php');

		$this->getRequest()->setControllerName('Rest' . ucfirst($api))->setActionName($method)->setDispatched(false);
	}

}