<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Protection_Spam_HoneyTrap_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('Protection.Spam.HoneyTrap', true);

		if (Aitsu_Config :: get('honeytrap.keyword') == null) {
			return;
		}

		$honeyTraps = array_flip(Aitsu_Config :: get('honeytrap.keyword')->toArray());
		if (!empty ($_POST)) {
			if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
				$ht = Aitsu_Persistence_Honeytrap :: factory();
				$ht->ip = $_SERVER["REMOTE_ADDR"];
				$ht->save();
				Aitsu_Ee_Cache_Page :: lifetime(0);
			}
		}

		$view = $this->_getView();
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