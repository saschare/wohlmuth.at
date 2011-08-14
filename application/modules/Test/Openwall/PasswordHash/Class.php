<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Test_Openwall_PasswordHash_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$view->result = Openwall_PasswordHash_Test :: test();

		return $view->render('index.phtml');
	}
}