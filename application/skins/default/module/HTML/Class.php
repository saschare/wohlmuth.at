<?php

class Skin_Module_HTML_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$index = str_replace('_', ' ', $this->_index);

		$output = '';
		if ($this->_get('HTML', $output)) {
			return $output;
		}

		$return = '';
		if (Aitsu_Registry :: isEdit()) {
			$return .= '<div style="padding-top:5px; padding-bottom:5px;">';
		} else {
			$return .= '';
		}

		$output = Aitsu_Content_Html :: get($index);

		$this->_save($output, 'eternal');

		if (Aitsu_Application_Status :: isEdit()) {
			return $return . $output . '</div>';
		} else {
			return $return . $output . '';
		}
	}
}