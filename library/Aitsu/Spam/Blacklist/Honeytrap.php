<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Honeytrap.php 18273 2010-08-23 11:17:17Z akm $}
 */

class Aitsu_Spam_Blacklist_Honeytrap extends Aitsu_Spam_Blacklist_Abstract {
	
	public function isAllowed() {

		return Aitsu_Persistence_Honeytrap :: factory()->getWeight($this->_ip) < 0.0001;
	}
}