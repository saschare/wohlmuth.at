<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$return = '';
		if (Aitsu_Application_Status :: isEdit()) {
			$return .= '<div style="padding-top:5px; padding-bottom:5px;">';
		} else {
			$return .= '<div>';
		}

		$output = Aitsu_Content_Html :: get($this->_context['rawIndex']);

		return $return . $output . '</div>';
	}
}