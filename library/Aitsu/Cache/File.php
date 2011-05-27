<?php


/**
 * File system cache.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: File.php 19974 2010-11-19 12:08:00Z akm $}
 */

class Aitsu_Cache_File extends Aitsu_Cache_Abstract {

	public function __construct() {

		$frontendOptions = array (
			'lifetime' => null,
			'automatic_serialization' => false
		);

		$cache_dir = APPLICATION_PATH . '/data/cache/';

		if (!is_dir($cache_dir)) {
			mkdir($cache_dir, 0777, true);
		}

		$backendOptions = array (
			'cache_dir' => $cache_dir
		);

		$this->cache = Zend_Cache :: factory('Output', 'File', $frontendOptions, $backendOptions);
	}

}