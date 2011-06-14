<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

class Module_Test_Openwall_PasswordHash_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();
		$view = $instance->_getView();
		
		$view->result = Openwall_PasswordHash_Test :: test();
		
		// $view->result = 'das ist ein test';

		return $view->render('index.phtml');
	}
}