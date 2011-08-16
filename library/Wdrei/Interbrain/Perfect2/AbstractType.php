<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Wdrei_Interbrain_Perfect2_AbstractType {

	protected $_data = array ();

	public function __set($key, $val) {

		$this->_data[$key] = $val;
	}

	public function __get($key) {

		if (!isset ($this->_data[$key])) {
			return null;
		}

		return $this->_data[$key];
	}
	
	public function __isset($key) {
		
		return isset($this->_data[$key]);
	}
}