<?php


/**
 * Abstract cache.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 20001 2010-11-19 19:49:56Z akm $}
 */

abstract class Aitsu_Cache_Abstract {

	protected $_id;
	protected $_cacheIfLoggedIn = false;
	protected $_disabled = null;
	protected $_cache = null;
	protected $_lifetime = null; // caching forever

	public function setId($id) {

		$this->_id = $id;
	}

	public function setCacheIfLoggedIn($set) {

		$this->_cacheIfLoggedIn = $set;
	}

	final protected function _isDisabled() {

		return !Aitsu_Application_Status :: isAllowCaching($this->_cacheIfLoggedIn);
	}

	public function load() {

		if ($this->_isDisabled()) {
			return false;
		}

		if ($this->cache != null) {
			return $this->cache->load($this->_id);
		}

		return false;
	}

	public function isValid() {

		if ($this->_isDisabled()) {
			return false;
		}

		if ($this->cache != null) {
			return $this->cache->test($this->_id);
		}

		return false;
	}

	public function save($data, $tags = null) {

		if ($this->_isDisabled() || is_null($this->cache)) {
			return;
		}

		if ($tags == null) {
			$this->cache->save($data, $this->_id, array (), $this->_lifetime);
		} else {
			$this->cache->save($data, $this->_id, $tags, $this->_lifetime);
		};
	}

	public function setLifetime($newLifetime) {

		$this->lifetime = $newLifetime;
	}

	public function remove() {

		if ($this->cache != null) {
			$this->cache->remove($this->_id);
		}
	}

	public function clean($tags = null, $mode = Zend_Cache :: CLEANING_MODE_MATCHING_ANY_TAG) {

		if ($this->cache != null) {
			if (empty ($tags)) {
				return $this->cache->clean(Zend_Cache :: CLEANING_MODE_ALL);
			}

			$this->cache->clean($mode, $tags);
		}
	}

}