<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_HTMLi_Class extends Aitsu_Ee_Module_Abstract {

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