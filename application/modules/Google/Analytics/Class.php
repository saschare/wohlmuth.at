<?php


/**
 * Google Analytics implementation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Google_Analytics_Class extends Aitsu_Module_Abstract {

	protected $_cacheIfLoggedIn = true;

	protected function _init() {
	}

	protected function _main() {

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {
		/*
		 * 1 hour.
		 */
		return 3600;
	}

}