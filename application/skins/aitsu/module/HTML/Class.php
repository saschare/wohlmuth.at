<?php

class Skin_Module_HTML_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();
		$index = str_replace('_', ' ', $context['index']);

		$output = '';
		if ($instance->_get('HTML_' . $context['index'], $output)) {
			return $output;
		}

		$return = '';
		if (Aitsu_Registry :: isEdit()) {
			$return .= '<div style="padding-top:5px; padding-bottom:5px;">';
		} else {
			$return .= '';
		}

		$output = Aitsu_Content_Html :: get($index);
		$instance->_save($output, 'eternal');

		if (Aitsu_Registry :: isEdit()) {
			return $return . $output . '</div>';
		} else {
			return $return . $output . '';
		}
	}
}