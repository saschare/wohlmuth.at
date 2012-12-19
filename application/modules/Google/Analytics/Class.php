<?php


/**
 * Google Analytics implementation.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Google_Analytics_Class extends Aitsu_Module_Abstract {

	protected $_cacheIfLoggedIn = true;

	protected function _main() {

		return Aitsu_Service_Google_Analytics :: getScript();
	}

	protected function _cachingPeriod() {
		/*
		 * 1 hour.
		 */
		return 3600;
	}

}