<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ScriptController extends Zend_Controller_Action {

	/**
	 * @since 2.1.0.0 - 29.12.2010
	 */
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
	}

	public function indexAction() {

		header("Content-type: text/javascript");
	}

	/**
	 * @since 2.1.0.0 - 28.12.2010
	 */
	public function treeAction() {

		$scripts = Aitsu_Persistence_View_Scripts :: getAll();
		$node = $this->getRequest()->getParam('node');
		$return = array ();

		if ($node == '0') {
			/*
			 * Return root node.
			 */
			foreach ($scripts as $key => $value) {
				$return[] = array (
					'id' => $key,
					'text' => $key,
					'type' => 'category',
					'iconCls' => 'treecat-online'
				);
			}
		} else {
			foreach ($scripts[$node] as $script) {
				$return[] = array (
					'id' => $script->id,
					'text' => $script->name,
					'type' => 'script',
					'iconCls' => 'tm-script'
				);
			}
		}

		$this->_helper->json($return);
	}

	/**
	 * @since 2.1.0.0 - 29.12.2010
	 */
	public function showAction() {

		header("Content-type: text/javascript");

		$exScript = $this->getRequest()->getParam('script');

		foreach (Aitsu_Persistence_View_Scripts :: getAll() as $category => $scripts) {
			foreach ($scripts as $script) {
				if ($exScript == $script->id) {
					$this->view->script = $script;
					return;
				}
			}
		}
	}

	/**
	 * @since 2.1.0.0 - 29.12.2010
	 */
	public function executeAction() {

		$id = $this->getRequest()->getParam('script');
		$step = $this->getRequest()->getParam('step');

		foreach (Aitsu_Persistence_View_Scripts :: getAll() as $category => $scripts) {
			foreach ($scripts as $script) {
				if ($id == $script->id) {
					break 2;
				}
			}
		}

		$class = $script->className;
		$class = new $class ($step);
		$response = $class->exec();

		$this->_helper->json($response->toArray());
	}
}