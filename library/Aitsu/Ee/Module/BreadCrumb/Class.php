<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_BreadCrumb_Class extends Aitsu_Ee_Module_Abstract {

	protected $type = 'navigation';

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('BreadCrumb', true);

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('BreadCrumb', $output)) {
			return $output;
		}

		$view->bc = Aitsu_Persistence_View_Category :: breadCrumb();
		$output = $view->render('index.phtml');
		$instance->_save($output, 'eternal');

		return $output;
	}
}