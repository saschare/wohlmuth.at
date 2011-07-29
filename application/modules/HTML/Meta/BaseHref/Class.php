<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Meta_BaseHref_Class extends Aitsu_Module_Abstract {
	
	protected $_allowEdit = false;

	protected function _main() {

		return '<base href="' . Aitsu_Config :: get('sys.webpath') . '" />';
	}
}