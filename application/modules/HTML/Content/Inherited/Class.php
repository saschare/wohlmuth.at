<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Content_Inherited_Class extends Aitsu_Module_Tree_Abstract {
	
	protected function _init() {

		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry :: isEdit()) {
			$startTag = '<div style="padding-top:5px; padding-bottom:5px;">';
			$endTag = '</div>';
		}

		$output = Aitsu_Content_Html :: getInherited($this->_index);

		return $startTag . $output . $endTag;
	}
}