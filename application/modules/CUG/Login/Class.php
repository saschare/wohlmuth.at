<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_CUG_Login_Class extends Aitsu_Module_Abstract {
	
	protected $_allowEdit = false;

	protected function _init() {

		$view = $this->_getView();
		
		/*
		 * Suppress the output it the user is already logged in.
		 */
		$user = Aitsu_Adm_User :: getInstance();
		if (!is_null($user)) {
			return '';
		}

		$output = $view->render('index.phtml');

		return $output;
	}

}