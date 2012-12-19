<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2013, w3concepts AG
 */
class Aitsu_Service_Google_Analytics {
	
	protected $_data = array();

	private function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}
	
	public function setAccount(string $val) {
		
		$this->_data['account'] = $val;
	}
	
	public function setDomainName(string $val) {
		
		$this->_data['domainName'] = $val;
	}
	
	public function setAllowLinker(boolean $val) {
		
		$this->_data['allowLinker'] = (boolean) $val;
	}
	
	public function addTrans(Aitsu_Service_Google_Analytics_Transaction $val) {
		
		$this->_data['transaction'][] = $val;
	}

}