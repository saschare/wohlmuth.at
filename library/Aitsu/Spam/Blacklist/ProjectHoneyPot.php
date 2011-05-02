<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ProjectHoneyPot.php 18302 2010-08-23 16:32:04Z akm $}
 */
 
class Aitsu_Spam_Blacklist_ProjectHoneyPot extends Aitsu_Spam_Blacklist_Abstract {
	
	public function isAllowed() {
		
		$ip = $_SERVER['REMOTE_ADDR'];

		$apiKey = Aitsu_Registry :: get()->config->spam->blacklist->projectHoneyPot;
		
		$lookup = $apiKey . '.' . implode('.', array_reverse(explode('.', $ip))) . '.dnsbl.httpbl.org';
		$result = explode('.', gethostbyname($lookup));

		return $result[0] != '127';
	}
}
