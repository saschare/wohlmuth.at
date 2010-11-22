<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: PluginController.php 18806 2010-09-17 08:14:54Z akm $}
 */

class PluginController extends Zend_Controller_Action {

	public function indexAction() {

		$area = $this->getRequest()->getParam('area');
		$plugin = $this->getRequest()->getParam('plugin');
		$action = $this->getRequest()->getParam('paction');
		$controller = $plugin . 'Plugin';

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'plugin.' . $area . '.' . $plugin
			))) {
			return;
		}

		$this->_helper->viewRenderer->setNoRender(true);

		include_once (APPLICATION_PATH . '/plugins/generic/' . $area . '/' . $plugin . '/Class.php');

		$this->view->setScriptPath(array (
			APPLICATION_PATH . '/plugins/generic/' . $area . '/' . $plugin . '/views/'
		));

		$this->getRequest()->setControllerName($controller)->setActionName($action)->setDispatched(false);
	}

	public function articleAction() {

		$plugin = $this->getRequest()->getParam('plugin');

		if (is_object($plugin)) {
			$plugin = $plugin->name;
		}

		$action = $this->getRequest()->getParam('paction');
		$controller = $plugin . 'Article';
		$this->_helper->viewRenderer->setNoRender(true);

		include_once (APPLICATION_PATH . '/plugins/article/' . $plugin . '/Class.php');

		$this->view->setScriptPath(array (
			APPLICATION_PATH . '/plugins/article/' . $plugin . '/views/'
		));

		$this->getRequest()->setControllerName($controller)->setActionName($action)->setDispatched(false);
	}

	public function dashboardAction() {

		$plugin = $this->getRequest()->getParam('plugin');

		if (is_object($plugin)) {
			$plugin = $plugin->name;
		}

		$action = $this->getRequest()->getParam('paction');
		$controller = $plugin . 'Dashboard';
		$this->_helper->viewRenderer->setNoRender(true);

		include_once (APPLICATION_PATH . '/plugins/dashboard/' . $plugin . '/Class.php');

		$this->view->setScriptPath(array (
			APPLICATION_PATH . '/plugins/dashboard/' . $plugin . '/views/'
		));

		$this->getRequest()->setControllerName($controller)->setActionName($action)->setDispatched(false);
	}

	public function categoryAction() {

		$plugin = $this->getRequest()->getParam('plugin');

		if (is_object($plugin)) {
			$plugin = $plugin->name;
		}

		$action = $this->getRequest()->getParam('paction');
		$controller = $plugin . 'Category';
		$this->_helper->viewRenderer->setNoRender(true);

		include_once (APPLICATION_PATH . '/plugins/category/' . $plugin . '/Class.php');

		$this->view->setScriptPath(array (
			APPLICATION_PATH . '/plugins/category/' . $plugin . '/views/'
		));

		$this->getRequest()->setControllerName($controller)->setActionName($action)->setDispatched(false);
	}
	
}