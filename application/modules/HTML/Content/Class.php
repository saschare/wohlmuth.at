<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Content_Class extends Aitsu_Module_Abstract {
	
	protected function _init() {

		$index = str_replace('_', ' ', $this->_index);

		$output = '';
		if ($instance->_get('HTML_' . $index, $output)) {
			return $output;
		}

		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry :: isEdit()) {
			$startTag = '<div style="padding-top:5px; padding-bottom:5px;">';
			$endTag = '</div>';
		}

		$output = Aitsu_Content_Html :: get($index);
		$this->_save($output, 'eternal');

		return $startTag . $output . $endTag;
	}
}