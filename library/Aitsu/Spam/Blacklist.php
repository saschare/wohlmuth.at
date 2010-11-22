<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Blacklist.php 18274 2010-08-23 11:17:25Z akm $}
 */

class Aitsu_Spam_Blacklist {

	protected function __construct() {
	}

	public function getInstance($source, $ip) {

		static $instance = array ();

		if (!isset ($instance[$source])) {
			$instance[$source] = new $source ($ip);
		}

		return $instance[$source];
	}

	public static function isSpam($ip) {

		if (!isset (Aitsu_Registry :: get()->config->spam->blacklist)) {
			return false;
		}
		
		$bl = Aitsu_Registry :: get()->config->spam->blacklist->toArray();
		foreach ($bl as $blacklist => $enabled) {
			if ($enabled) {
				$blacklist = 'Aitsu_Spam_Blacklist_' . ucfirst($blacklist);
				$blacklist = new $blacklist($_SERVER['REMOTE_ADDR']);
				if (!$blacklist->isAllowed()) {
					return true;
				}
			}
		}
		
		return false;
	}
}