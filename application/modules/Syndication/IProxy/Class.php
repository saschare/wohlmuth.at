<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Syndication_IProxy_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$page = Aitsu_Content_Config_Link :: set($this->_index, 'IProxy.Source', 'Article', 'Source');
		
		return $view->render('index.phtml');
	}
}