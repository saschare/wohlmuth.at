<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */

class Module_Protection_Spam_ReCaptcha_Class extends Aitsu_Module_Abstract {
	
	protected $_allowEdit = false;

	protected function _init() {

		return $view->render('index.phtml');
	}
}