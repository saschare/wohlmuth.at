<?php


/**
 * HTML (with inherition) as ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17813 2010-07-29 09:01:04Z akm $}
 */

class Aitsu_Ee_Module_HTMLi_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'HTMLi',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Editable area with inherition function. It inherits its content from the start article of the same level or the closed higher level containing content.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536b-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();

		$output = '';
		if ($instance->_get('HTMLi_' . $context['index'], $output)) {
			return $output;
		}

		$index = str_replace('_', ' ', $context['index']);

		$show = Aitsu_Ee_Config_Radio :: set($context['index'], 'Show', '', array (
			'Yes' => 'yes',
			'No' => 'no'
		), 'Show content');

		$show = empty ($show) ? 'yes' : $show;

		$return = '';
		if (Aitsu_Registry :: isEdit()) {
			$return .= '<div style="padding-top:5px; padding-bottom:5px;">';
		} else {
			$return .= '<div>';
		}

		if ($show == 'no' && !Aitsu_Registry :: isEdit()) {
			return '';
		}

		if ($show == 'no' && Aitsu_Registry :: isEdit()) {
			return '<div><strong>Output suppressed. Click here to activate output.</strong></div>';
		}

		$return = $return . Aitsu_Content_Html :: getInherited($index) . '</div>';

		$instance->_save($return, 'eternal');

		return $return;
	}
}