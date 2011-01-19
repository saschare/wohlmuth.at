<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Ee_Module_Rating_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Rating', true);
		$instance = new self();

		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		$template = isset($params->template) ? $params->template : 'index';

		$output = '';
		if ($instance->_get('Rating', $output)) {
			return $output;
		}

		$view = $instance->_getView();
		$view->rating = Aitsu_Persistence_Rating(Aitsu_Registry :: get()->env->idartlang)->rating;
		
		$output = $view->render($template . '.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}

}