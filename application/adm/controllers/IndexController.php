<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class IndexController extends Zend_Controller_Action {

	public function init() {
		
		if ($this->getRequest()->getParam('ajax')) {
			header("Content-type: text/javascript");
			$this->_helper->layout->disableLayout();
		}
	}

	public function indexAction() {
	
		$this->_loadPlugins();
	}
	
	protected function _loadPlugins() {

		$plugins = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/dashboard', 'Class.php');
		$this->view->plugins = array ();
		foreach ($plugins as $plugin) {
			$parts = explode('/', $plugin);
			$pluginName = $parts[count($parts) - 2];
			include_once ($plugin);
			$controller = ucfirst($pluginName) . 'Dashboard';
			$controllerClass = $controller . 'Controller';
			$registry = call_user_func(array (
				$controllerClass,
				'register'
			));
			if ($registry->enabled) {
				$this->view->plugins[] = $registry;
			}
		}
	}
}