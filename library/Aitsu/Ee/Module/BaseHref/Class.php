<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_BaseHref_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('BaseHref', true);

		return '<base href="' . Aitsu_Registry :: get()->config->sys->webpath . '" />';
	}
}