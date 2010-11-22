<?php


/**
 * aitsu registry entry.
 * 
 * The class provides a mechanism to avoid not isset notices
 * occuring when trying to access a member of a non-existing
 * object.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Entry.php 15381 2010-03-17 08:52:35Z akm $}
 */

class Aitsu_Registry_Entry {

	protected $registry = array ();

	public static function factory() {

		$instance = new self();
		return $instance;
	}

	public function __get($name) {

		if (!isset ($this->registry[$name]) && $this->_isLeaf($name)) {
			/*
			 * The specified member is a leaf and not yet set. We therefore
			 * return null.
			 */
			return null;
		}

		if (!isset ($this->registry[$name]) || $this->registry[$name] == null) {
			$this->registry[$name] = self :: factory();
		}

		return $this->registry[$name];
	}

	public function __set($name, $value) {

		$this->registry[$name] = $value;

		return $this;
	}

	public function __isset($name) {

		if (!isset ($this->registry[$name])) {
			return false;
		}

		return true;
	}

	public function __toString() {

		return '';
	}

	protected function _isLeaf($name) {

		/*
		 * Leafs are nodes in the registry, which, by definition, are the
		 * last nodes in the tree and threrefore may not be of type
		 * Aitsu_Registry_Entry.
		 */

		$leafs = array (
			'idart',
			'idcat',
			'idartlang',
			'idlang',
			'client'
		);

		return in_array($name, $leafs);
	}
}