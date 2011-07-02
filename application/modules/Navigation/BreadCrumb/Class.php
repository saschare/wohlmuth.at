<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Navigation_BreadCrumb_Class extends Aitsu_Module_Abstract {

	protected $type = 'navigation';

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Navigation.BreadCrumb', true);

		$view = $this->_getView();

		$output = '';
		if ($this->_get('Navigation.BreadCrumb', $output)) {
			return $output;
		}

		$view->bc = Aitsu_Persistence_View_Category :: breadCrumb();
		$output = $view->render('index.phtml');
		$this->_save($output, 'eternal');

		return $output;
	}
}