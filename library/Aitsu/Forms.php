<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright w3concepts AG
 */

class Aitsu_Forms {
	
	protected $_id;
	protected $_config = null;
	
	protected function __construct($id, $ini) {
		
		$this->_id = $id;
		
		if ($ini != null) {
			if (is_object($ini)) {
				$this->_config = $ini;
			} else {
				$this->_config = new Zend_Config_Ini($ini, null);
			}
		}
	}
	
	public function factory($id, $ini = null) {
		
		static $instance = array();
		
		if (!isset($instance[$id])) {
			$instance[$id] = new self($id, $ini);
		}
		
		return $instance[$id];
	}
	
	public function isValid() {
		
		return false;
	}
}