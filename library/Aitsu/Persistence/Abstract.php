<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 18796 2010-09-16 20:15:26Z akm $}
 */

abstract class Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = array();

	protected function __construct($id) {

		$this->_id = $id;
	}

	abstract public function factory($id = null);

	abstract public function load();

	abstract public function __get($key);

	abstract public function __set($key, $value);

	abstract public function save();

	public function toArray() {

		return $this->_data;
	}

	public function setValues(array $values) {

		if (!is_array($this->_data)) {
			$this->_data = array();
		}

		$this->_data = array_merge($this->_data, $values);

		return $this;
	}
}