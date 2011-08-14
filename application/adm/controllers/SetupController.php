<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class SetupController extends Zend_Controller_Action {

	/**
	 * @since 2.1.0.0 - 29.12.2010
	 */
	public function init() {
		
		$this->_helper->layout->disableLayout();

		if (Aitsu_Adm_User :: getInstance()->getId() == 'setup') {
			return;
		} 

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'script',
				'action' => 'execute'
			))) {
			throw new Exception(Aitsu_Translate :: translate('Access denied.'));
		}
	}

	public function indexAction() {

		$installScript = Aitsu_Persistence_View_Scripts :: getAll();
		$installScript = $installScript['Installation'];

		foreach ($installScript as $script) {
			if ($script->name == 'Setup') {
				$this->view->script = $script;
				return;
			}
		}
	}

}