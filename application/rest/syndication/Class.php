<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RestSyndicationController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
	}

	public function indexAction() {

		echo 'OK';
	}

	public function treeAction() {
		
		$idcat = $this->getRequest()->getParam('idcat');
		
		echo var_export($idcat, true);
	}
}