<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */

class Module_Protection_Spam_ReCaptcha_Class extends Aitsu_Module_Abstract {

	protected $_allowEdit = true;

	protected function _init() {
		
		$view = $this->_getView();

		$theme = Aitsu_Content_Config_Radio :: set($this->_index, 'ReCaptcha.theme', '', array (
			'Rot (default)' => 'red',
			'Weiss' => 'white',
			'Schwarz' => 'blackglass',
			'Clean' => 'clean',
			'Custom' => 'custom'
		), 'Theme');
		
		$template = $theme == 'custom' ? 'custom' : 'index';
		$view->theme = empty($theme) ? 'red' : $theme;

		if (Aitsu_Application_Status :: isEdit()) {
			return;
		}

		$return = $view->render('init.phtml');
		$return .= $view->render($template . '.phtml');
		
		return $return;
	}
}