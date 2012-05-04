<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Cache_Apc extends Aitsu_Cache_Abstract {

	/**
	 * @return Aitsu_Cache_File Constructor.
	 */
	public function __construct() {

		$frontendOptions = array (
			'lifetime' => null,
			'automatic_serialization' => false
		);

		$this->cache = Zend_Cache :: factory('Output', 'Apc', $frontendOptions);
	}

}