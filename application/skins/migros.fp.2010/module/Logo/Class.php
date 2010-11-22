<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19583 2010-10-26 15:50:51Z akm $}
 */

class Skin_Module_Logo_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('Logo', true);

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('Logo', $output)) {
			return $output;
		}

		$output = $view->render('index.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}

}