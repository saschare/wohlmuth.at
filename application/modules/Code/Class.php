<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Code_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry :: isEdit()) {
			$startTag = '<div style="padding-top:5px; padding-bottom:5px;">';
			$endTag = '</div>';
		}

		$output = Aitsu_Content_Text :: get($this->_index);

		return '' .
		'<script type="application/x-aitsu" src="Code.GeSHi:php">' . "\n" .
		$output . "\n" .
		'</script>';
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}