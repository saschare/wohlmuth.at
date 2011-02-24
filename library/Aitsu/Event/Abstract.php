<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

abstract class Aitsu_Event_Abstract implements Aitsu_Event_Interface {

	protected $_signature = null;
	protected $_context = null;

	protected final function __construct($signature, $context) {

		$this->_signature = explode('.', $signature);
		$this->_context = $context;

		Aitsu_Event_Dispatcher :: getInstance()->raise($this);
	}

	//abstract public static function raise($signature, $context);

	public final function getSignature() {

		return $this->_signature;
	}

	public final function __get($key) {

		if (is_object($this->_context)) {
			if (isset ($this->_context-> $key)) {
				return $this->_context-> $key;
			}
			return null;
		}

		if (isset ($this->_context[$key])) {
			return $this->_context[$key];
		}

		return null;
	}
}