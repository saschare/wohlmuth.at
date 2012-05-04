<?php


/**
 * File system cache. The class extends Aitsu_Cache_Abstract
 * and overwrites the constructor to provide a Zend Cache based
 * on file system.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Cache_File extends Aitsu_Cache_Abstract {

	/**
	 * @return Aitsu_Cache_File Constructor.
	 */
	public function __construct() {

		$frontendOptions = array (
			'lifetime' => null,
			'automatic_serialization' => false
		);

		if (isset (Aitsu_Registry :: get()->config->cache->dir)) {
			$cache_dir = Aitsu_Registry :: get()->config->cache->dir;
		} else {
			$cache_dir = APPLICATION_PATH . '/data/cache/';
		}

		if (!is_dir($cache_dir)) {
			mkdir($cache_dir, 0777, true);
		}

		$backendOptions = array (
			'cache_dir' => $cache_dir,
			'file_locking' => false,
			'read_control' => false,
			'hashed_directory_level' => 2
		);

		$this->cache = Zend_Cache :: factory('Output', 'File', $frontendOptions, $backendOptions);
	}

}