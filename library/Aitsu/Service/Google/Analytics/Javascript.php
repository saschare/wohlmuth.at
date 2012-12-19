<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Javascript {
	
	protected $_val;
	
	public function __construct($val) {
		
		$this->_val = $val;
	}
	
	public function toString() {
		
		return $this->_val;
	}
}