<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

include_once (APPLICATION_PATH . '/modules/Schema/Org/Article/Class.php');

class Module_Schema_Org_NewsArticle_Class extends Module_Schema_Org_Article_Class {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();

		$view->index = $this->_index;

		return $view;
	}
}