<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

include_once (APPLICATION_PATH . '/modules/Schema/Org/Rating/Class.php');

class Module_Schema_Org_AggregateRating_Class extends Module_Schema_Org_Rating_Class {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();

		$view->ratingCount = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.AggregateRating.RatingCount', 'Rating count', 'AggregateRating');
		$view->reviewCount = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.AggregateRating.ReviewCount', 'Review count', 'AggregateRating');
		
		$view->index = $this->_index;

		return $view;
	}
}