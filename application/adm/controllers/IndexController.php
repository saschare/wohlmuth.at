<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class IndexController extends Zend_Controller_Action {

	public function init() {
		
		if ($this->getRequest()->getParam('ajax')) {
			$this->_helper->layout->disableLayout();
		}
	}

	public function indexAction() {
	
		
	}
	
	public function managementAction() {
		
		$this->_redirect('acl/profil');
	}
}