<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class SynchronizationCategoryController extends Aitsu_Adm_Plugin_Controller {

	const ID = '';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register($idcat) {

		return (object) array (
			'name' => 'synchronization',
			'tabname' => Aitsu_Translate :: translate('Synchronization'),
			'enabled' => true,
			'position' => 1,
			'id' => self :: ID
		);		
	}

	public function indexAction() {
		
		$this->view->idcat = $this->getRequest()->getParam('idcat');
	}
}