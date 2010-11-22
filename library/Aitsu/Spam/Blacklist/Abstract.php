<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 18273 2010-08-23 11:17:17Z akm $}
 */

abstract class Aitsu_Spam_Blacklist_Abstract {
	
	protected $_ip = null;
	
	public function __construct($ip) {
		
		$this->_ip = $ip;
	}
	
	abstract public function isAllowed();
}