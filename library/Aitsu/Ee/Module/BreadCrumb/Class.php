<?php


/**
 * BreadCrumb ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Aitsu_Ee_Module_BreadCrumb_Class extends Aitsu_Ee_Module_Abstract {

	protected $type = 'navigation';

	public static function about() {

		return (object) array (
			'name' => 'BreadCrumb',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns a bread crumb navigation.'),
			'type' => 'Navigation',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725361-c565-11df-851a-0800200c9a66'
		);
	}

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