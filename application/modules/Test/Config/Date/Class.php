<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

class Module_Test_Config_Date_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();
		$view = $instance->_getView();
		
		$view->result = Aitsu_Content_Config_Date :: set($context['index'], 'Test.Config.Date', 'Date', 'Date');

		return $view->render('index.phtml');
	}
}