<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_CUG_Login_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$output = $view->render('index.phtml');

		return $output;
	}

}