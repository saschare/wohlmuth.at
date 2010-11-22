<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 19481 2010-10-21 15:15:07Z akm $}
 */

abstract class Aitsu_Event_Abstract {
	
	protected $_signature = null;
	protected $_context = null;

	protected final function __construct($signature, $context) {
		
		$this->_signature = explode('.', $signature);
		$this->_context = $context;
		
		Aitsu_Event_Dispatcher :: getInstance()->raise($this);
	}

	abstract public static function raise($signature, $context);
	
	public final function getSignature() {
		
		return $this->_signature;
	}
	
	public final function __get($key) {
		
		if (isset($this->_context[$key])) {
			return $this->_context[$key];
		}
		
		return null;
	}
}