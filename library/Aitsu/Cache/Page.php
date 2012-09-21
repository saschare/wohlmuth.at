<?php


/**
 * Caches pages in the database
 *  
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Page.php 17814 2010-07-29 09:22:07Z akm $}
 */

class Aitsu_Cache_Page {

	protected $lifetime;
	protected $db, $requestHash;

	protected function __construct($requestHash) {

		$this->lifetime = Aitsu_Registry :: get()->config->cache->page->lifetime;
		$this->requestHash = $requestHash;
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self(REQUEST_HASH);

			if (array_key_exists(strtolower(Aitsu_Registry :: get()->config->cache->clear->key), array_change_key_case($_GET, CASE_LOWER))) {
				$instance->clearCache();
			}
		}

		return $instance;
	}

	public function saveFs($content) {

		$files = glob(CACHE_PATH . '/' . REQUEST_HASH . '.*.html');
		if ($files !== false) {
			foreach (glob(CACHE_PATH . '/' . $this->requestHash . '.*.html') as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
		}

		$timeStamp = time() + $this->lifetime;
		$etag = hash('md4', $content);

		if ($handle = fopen(CACHE_PATH . '/' . $this->requestHash . '.' . $timeStamp . '.' . $etag . '.html', "a")) {
			fwrite($handle, $content);
			fclose($handle);
		}
	}

	public static function lifetime($maxLifetime) {

		self :: getInstance()->setLifetime($maxLifetime);
	}

	public function setLifetime($lifetime) {

		$this->lifetime = min($this->lifetime, $lifetime);
	}

	public function clearCache() {

		$files = glob(CACHE_PATH . '/*');
		if ($files) {
			foreach (glob(CACHE_PATH . '/*') as $file) {
				if (is_file($file)) {
					unlink($file);
				}
			}
		}
	}
}