<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Meta_BaseHref_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.BaseHref', true);

		return '<base href="' . Aitsu_Config :: get('sys.webpath') . '" />';
	}
}