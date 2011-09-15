<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Aitsu_Article_Policy_Abstract {
	
	protected $_idartlang = null;
	protected $_statement = null;
	protected $_message = '';
	
	public function __construct($statement, $idartlang = null) {
		
		$this->_idartlang = $idartlang;
		$this->_statement = $this->_evalStatement($statement);
	}
	
	public function isFullfilled() {
		
		return false;
	}
	
	public function getMessage() {
		
		if ($this->isFullfilled()) {
			return null;
		}
		
		return $this->_message;
	}
	
	protected function _evalStatement($statement) {
		
		return $statement;
	}
	
}