<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Form_Processor_Serialize implements Aitsu_Form_Processor_Interface {
	
	protected $_token;
	protected $_redirect = false;
	
	protected function __construct($token) {
		
		$this->_token = $token;
		$this->_prop = Aitsu_Core_Article_Property :: factory();
	}

	public static function getInstance($token, $redirect = false) {

		static $instance = array();
		
		if (!isset($instance[$token])) {
			$instance[$token] = new self($token);
		}
		
		$instance[$token]->_redirect = $redirect;
		
		return $instance[$token];
	}
	
	public function process() {
		
		$this->_prop->setValue('FormSerialization_', $this->_token, $_POST, 'serialized');
		
		if (!$this->_redirect) {
			return;
		}
		
		ob_end_clean();
		header('Location: ' . $this->_redirect);
		exit(0);
	}
	
	public function getValue() {
		
		return $this->_prop->getValue('FormSerialization_', $this->_token)->value;
	}
}