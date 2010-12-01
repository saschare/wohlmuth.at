<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_CUG_Login_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'CugLogin',
			'description' => Aitsu_Translate :: translate('Login mask.'),
			'type' => array (
				'Content'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4cf40921-b0e0-4158-af82-11547f000101'
		);
	}

	public static function init($context) {

		$instance = new self();

		$view = $instance->_getView();

		$output = $view->render('index.phtml');

		return $output;
	}

}