<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Example_ForHumansOnly_Class extends Aitsu_Module_Abstract {
	
	protected $_allowEdit = false;
	protected $_forHumanEyesOnly = 'verify';

	protected function _main() {

		$view = $this->_getView();
		return $view->render('index.phtml');
	}

	/**
	 * If you omit this method, no caching is made. Specifiy the caching
	 * period in seconds. 0 means no caching.
	 */
	protected function _cachingPeriod() {

		return 365 * 24 * 60 * 60;
	}
}