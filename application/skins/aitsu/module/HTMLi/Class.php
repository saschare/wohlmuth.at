<?php

class Skin_Module_HTMLi_Class extends Aitsu_Ee_Module_Abstract {

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
			$return .= '';
		}

		if ($show == 'no' && !Aitsu_Application_Status :: isEdit()) {
			return '';
		}

		if ($show == 'no' && Aitsu_Application_Status :: isEdit()) {
			return '<div class="padding:5px 0;">| HTMLi :: ' . $index . ' Output suppressed. Click here to activate output. |</div>';
		}

		if (Aitsu_Application_Status :: isEdit()) {
			$return = $return . Aitsu_Content_Html :: getInherited($index) . '</div>';
		} else {
			$return = $return . Aitsu_Content_Html :: getInherited($index) . '';
		}

		$instance->_save($return, 'eternal');

		return $return;
	}
}