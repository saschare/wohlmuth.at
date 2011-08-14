<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Navigation_BreadCrumb_Class extends Aitsu_Module_Tree_Abstract {

	protected $type = 'navigation';
	protected $_allowEdit = false;

	protected function _main() {

		$view = $this->_getView();

		$view->bc = Aitsu_Persistence_View_Category :: breadCrumb();

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}