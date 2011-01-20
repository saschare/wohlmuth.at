<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Ee_Module_Rating_Class extends Aitsu_Ee_Module_Abstract {
	
	const PARAMNAME = '4d37f1ee-ebf0-46e8-8a95-6207b24d50ae';

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Rating', true);
		$instance = new self();

		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		$template = isset($params->template) ? $params->template : 'index';
		
		$view = $instance->_getView();
		$view->paramName = self :: PARAMNAME;
		
		if (isset($_POST[self :: PARAMNAME]) && is_numeric($_POST[self :: PARAMNAME])) {
			$view->readonly = true;
		} else {
			$view->readonly = false;
		}

		$output = '';
		if ($instance->_get('Rating', $output)) {
			return $output;
		}

		$rating = Aitsu_Persistence_Rating :: factory(Aitsu_Registry :: get()->env->idartlang);
		$view->rating = $rating->rating;
		$view->votes = $rating->votes;
		
		$output = $view->render($template . '.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}

}