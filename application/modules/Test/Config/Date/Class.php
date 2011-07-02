<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Test_Config_Date_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$view->result = Aitsu_Content_Config_Date :: set($this->_index, 'Test.Config.Date', 'Date', 'Date');

		return $view->render('index.phtml');
	}
}