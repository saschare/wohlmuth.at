<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_CodeHtml_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$output = '';
		if ($this->_get('CodeHtml', $output)) {
			return $output;
		}

		$text = Aitsu_Content_Text :: get($this->_index, 0);

		$text = (empty ($text) && Aitsu_Application_Status :: isEdit()) ? '<html>....' : $text;

		$output = '<script type="application/x-aitsu" src="Code.GeSHi:html4strict">' . $text . '</script>';
		
		$this->_save($output, 'eternal');

		return $output;
	}

}