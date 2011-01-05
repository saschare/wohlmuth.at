<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Ee_Module_HTML_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'HTML',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Editable area rendered with the built-in CK editor.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536a-c565-11df-851a-0800200c9a66'
		);
	}

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