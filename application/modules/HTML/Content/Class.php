<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Content_Class extends Aitsu_Ee_Module_Abstract {
	
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
			$return .= '<div>';
		}

		$output = Aitsu_Content_Html :: get($index);
		$instance->_save($output, 'eternal');

		return $return . $output . '</div>';
	}
}