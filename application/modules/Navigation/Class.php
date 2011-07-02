<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Navigation_Class extends Aitsu_Module_Abstract {

	protected $type = 'navigation';

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Navigation', true);

		$user = Aitsu_Adm_User :: getInstance();
		$template = isset ($this->_params->template) ? $this->_params->template : 'index';

		$output = '';
		if ($user == null && $this->_get('Navigation_' . $template, $output)) {
			return $output;
		}

		/*
		 * if $this->_params->idcat is not numeric, we have to assume 
		 * that the string represents a configuration value available.
		 */
		if (!is_numeric($this->_params->idcat)) {
			$this->_params->idcat = Aitsu_Config :: get($this->_params->idcat);
		}

		$view = $this->_getView();
		$view->nav = Aitsu_Persistence_View_Category :: nav($this->_params->idcat);

		$output = $view->render($template . '.phtml');

		if ($user == null) {
			$this->_save($output, 'eternal');
		}

		return $output;
	}

}