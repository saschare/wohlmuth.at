<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Community_Rating_Class extends Aitsu_Module_Abstract {

	const PARAMNAME = '4d37f1ee-ebf0-46e8-8a95-6207b24d50ae';

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Rating', true);

		$template = isset ($this->_params->template) ? $this->_params->template : 'index';

		$view = $this->_getView();
		$view->paramName = self :: PARAMNAME;

		if (isset ($_POST[self :: PARAMNAME]) && is_numeric($_POST[self :: PARAMNAME]) && $_POST[self :: PARAMNAME] > 0) {
			$view->readonly = true;
			Aitsu_Persistence_Rating :: rate($_POST[self :: PARAMNAME]);
			$this->_remove('Rating');

			$rating = Aitsu_Persistence_Rating :: factory(Aitsu_Registry :: get()->env->idartlang);
			$view->rating = $rating->rating;
			$view->votes = $rating->votes;

			return $view->render($template . '.phtml');
		} else {
			$view->readonly = false;
		}

		$output = '';
		if ($this->_get('Rating', $output)) {
			return $output;
		}

		$rating = Aitsu_Persistence_Rating :: factory(Aitsu_Registry :: get()->env->idartlang);
		$view->rating = $rating->rating;
		$view->votes = $rating->votes;

		$output = $view->render($template . '.phtml');

		$this->_save($output, 'eternal');

		return $output;
	}

}