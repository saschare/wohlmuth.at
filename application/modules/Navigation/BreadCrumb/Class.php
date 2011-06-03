<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Navigation_BreadCrumb_Class extends Aitsu_Ee_Module_Abstract {

	protected $type = 'navigation';

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Navigation.BreadCrumb', true);

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('Navigation_BreadCrumb', $output)) {
			return $output;
		}

		$view->bc = Aitsu_Persistence_View_Category :: breadCrumb();
		$output = $view->render('index.phtml');
		$instance->_save($output, 'eternal');

		return $output;
	}
}