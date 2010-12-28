<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ScriptController extends Zend_Controller_Action {

	protected $_scripts;

	public function init() {

		if (isset (Aitsu_Registry :: get()->allowTempAccess) && Aitsu_Registry :: get()->allowTempAccess) {
			/*
			 * Give the user temporary access to the script area for setup purposes.
			 */
		} else {
			if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
					'area' => 'script',
					'action' => 'execute'
				))) {
				throw new Exception(Aitsu_Translate :: translate('Access denied.'));
			}
		}
		
		$this->_helper->layout->disableLayout();

		// $this->_scripts = Aitsu_Persistence_View_Scripts :: getAll();
	}

	public function indexAction() {
		
		header("Content-type: text/javascript");
	}
	
	/**
	 * @since 2.1.0.0 - 28.12.2010
	 */
	public function treeAction() {
		
		$scripts = Aitsu_Persistence_View_Scripts :: getAll();
		
		trigger_error(var_export($scripts, true));
		
		$this->_helper->json($return);
	}

	public function __call($method, $params) {

		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('exec') != null) {
			return $this->_exec();
		}

		$class = $this->getRequest()->getParam('show');

		if ($class != null && !class_exists($class)) {
			return $this->_helper->redirector('index');
		}

		$this->view->scriptName = $class == null ? 'Scripts' : call_user_func(array (
			$class,
			'getName'
		));

		$subNavi = $this->view->partial('script/subnav.phtml', array (
			'scripts' => Aitsu_Persistence_View_Scripts :: getAll(),
			'category' => substr($method, 0, -6)
		));
		$this->view->placeholder('left')->set($subNavi);

		echo $this->view->render('script/executionwindow.phtml');
	}

	protected function _exec() {

		$this->_helper->layout->disableLayout();

		$class = $this->getRequest()->getParam('exec');
		$class = new $class ($this->getRequest()->getParam('step'));
		$response = $class->exec();

		$this->_helper->json($response->toArray());
	}
}