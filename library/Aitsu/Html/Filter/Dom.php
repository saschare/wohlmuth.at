<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Dom.php 19908 2010-11-16 16:25:49Z akm $}
 */

class Aitsu_Html_Filter_Dom {
	
	protected $_dom;

	protected function __construct($html) {

		$this->_dom = DOMDocument :: loadHTML($html);
	}

	public static function factory($html) {

		$instance = new self($html);
		
		return $instance;
	}
	
	public function byXPath($expression) {
		
		$xpath = new DOMXPath($this->_dom);
    	$el = $xpath->query($expression)->item(0);
    	
    	return $this->_dom->saveXML($el);
	}
}