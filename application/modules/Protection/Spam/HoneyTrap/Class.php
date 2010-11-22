<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19943 2010-11-18 19:17:08Z akm $}
 */

class Module_Protection_Spam_HoneyTrap_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'Honeytrap',
			'description' => Aitsu_Translate :: translate('Honeytrap to trap spam bots in a black list.'),
			'type' => 'Spam protection',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '0.1.0',
			'status' => 'in development',
			'url' => null,
			'id' => '4ce57475-7cf4-4a24-956f-4bd97f000101'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('Protection.Spam.HoneyTrap', true);

		if (!isset (Aitsu_Registry :: get()->config->honeytrap->keyword)) {
			return '';
		}
		
		$honeyTraps = array_flip(Aitsu_Registry :: get()->config->honeytrap->keyword->toArray());
		if (!empty ($_POST)) {
			if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
				$ht = Aitsu_Persistence_Honeytrap :: factory();
				$ht->ip = $_SERVER["REMOTE_ADDR"];
				$ht->save();
				Aitsu_Ee_Cache_Page :: lifetime(0);
			}
		}

		$view = $instance->_getView();
		$view->keyword = array_rand($honeyTraps);
		$view->showForm = count(array_intersect_key($honeyTraps, $_GET)) > 0;

		$templates = array (
			'a',
			'b',
			'c',
			'd',
			'e'
		);
		shuffle($templates);

		return $view->render($templates[0] . '.phtml');
	}
}