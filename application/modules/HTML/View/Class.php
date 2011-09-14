<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_HTML_View_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$idart = Aitsu_Content_Config_Link :: set($this->_index, 'HTML.View.Source', 'Source', 'Source article');
		$idart = preg_replace('/[^0-9]/', '', $idart);

		if (empty ($idart)) {
			return '';
		}
		
		Aitsu_Content_Edit :: register(false);
		$output = Aitsu_Content :: get($this->_index, Aitsu_Content :: HTML, $idart, null, 0);
		Aitsu_Content_Edit :: register();

		return $output;
	}
}