<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Schema_Org_Rating_Class extends Aitsu_Module_SchemaOrg_Abstract {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();

		$view->bestRating = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Rating.bestRating', 'Best', 'Rating');
		$view->ratingValue = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Rating.ratingValue', 'Value', 'Rating');
		$view->worstRating = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Rating.worstRating', 'Worst', 'Rating');

		return $view;
	}
}