<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ConfigController.php 19396 2010-10-19 15:59:00Z akm $}
 */

class ClipboardController extends Zend_Controller_Action {

	public function indexAction() {
	}

	public function addarticleAction() {

		$idart = Aitsu_Util_Type :: number($this->getRequest()->getParam('id'));

		Aitsu_Registry :: get()->session->clipboard->articles[] = $idart;

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => sprintf(Aitsu_Translate :: translate('Article with ID %s copied to clipboard.'), $idart)
		));
	}
}