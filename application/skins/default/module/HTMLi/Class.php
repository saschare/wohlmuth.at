<?php

class Skin_Module_HTMLi_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$output = '';
		if ($this->_get('HTMLi', $output)) {
			return $output;
		}
		
		$index = str_replace('_', ' ', $this->_index);

		$show = Aitsu_Ee_Config_Radio :: set($this->_index, 'Show', '', array (
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

		$this->_save($return, 'eternal');

		return $return;
	}
}