<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Navigation_Class extends Aitsu_Ee_Module_Abstract {

	protected $type = 'navigation';

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Navigation', true);
		$instance = new self();

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$user = Aitsu_Adm_User :: getInstance();
		$template = isset ($params->template) ? $params->template : 'index';

		$output = '';
		if ($user == null && $instance->_get('Navigation_' . $template . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {
			return $output;
		}
		
		/*
		 * if $params->idcat is not numeric, we have to assume that the string
		 * represents a configuration value available.
		 */
		if (!is_numeric($params->idcat)) {
			$params->idcat = Aitsu_Config :: get($params->idcat);
		}

		$view = $instance->_getView();
		$view->nav = Aitsu_Persistence_View_Category :: nav($params->idcat);

		$output = $view->render($template . '.phtml');

		if ($user == null) {
			$instance->_save($output, 'eternal');
		}

		return $output;
	}

}