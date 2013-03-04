<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Statistics_HitTrigger_Class extends Aitsu_Module_Abstract {
	
	protected $_allowEdit = false;

	protected function _init() {

		if (isset ($_REQUEST['renderOnly'])) {
			/*
			 * Disable caching.
			 */
			Aitsu_Registry :: get()->config->cache->page->enable = false;

			/*
			 * Count the current hit.
			 */
			Aitsu_Persistence_Hit :: hit();

			/*
			 * Return an empty gif.
			 */
			Aitsu_Util :: endAndCleanOutputBuffering();
			header("Content-type: image/gif");
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
			readfile(dirname(__FILE__) . '/0.gif');
			exit (0);
		}

		if (Aitsu_Registry :: isEdit()) {
			return '';
		}

		return '<img src="' . Aitsu_Config :: get('sys.mainDir') . '{ref:idart-' . Aitsu_Registry :: get()->env->idart . '}?renderOnly=HitTrigger" />';
	}
}