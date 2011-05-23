<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class SynchronizationCategoryController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4dd4ec6a-3828-4a9c-95de-0adf7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register($idcat) {

		$pos = self :: getPosition($idcat, 'synchronization', 'category');

		return (object) array (
			'name' => 'synchronization',
			'tabname' => Aitsu_Translate :: translate('Synchronization'),
			'enabled' => $pos,
			'position' => $pos,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->idcat = $this->getRequest()->getParam('idcat');
	}
}