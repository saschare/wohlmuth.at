<?php


/**
 * Index controller.
 * @version $Id: IndexController.php 18775 2010-09-15 07:54:09Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class IndexController extends Zend_Controller_Action {

	public function init() {
		/* Initialize action controller here */
	}

	public function indexAction() {
	
		// $this->_redirect('data');	
	}
	
	public function managementAction() {
		
		$this->_redirect('acl/profil');
	}
}