<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_Code_Class extends Aitsu_Module_Abstract {

	protected function _main() {

		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry :: isEdit()) {
			$startTag = '<div style="padding-top:5px; padding-bottom:5px;">';
			$endTag = '</div>';
		}

		$types = array (
			'SQL' => 'sql',
			'PHP' => 'php',
			'HTML' => 'html',
			'ASP' => 'asp',
			'Java' => 'java',
			'Javascript' => 'javascript',
			'XML' => 'xml'
		);
		ksort($types);

		$type = Aitsu_Content_Config_Radio :: set($this->_index, 'Code.Type', 'Type', $types, 'Type');

		$output = Aitsu_Content_Text :: get($this->_index);

		/*
		 * Escape shortcodes within the code.
		 */
		$output = str_replace(array('[', ']', '<', '>'), array('&aitsuBracketLeft;', '&aitsuBracketRight', '&aitsuLessThan;', '&aitsuGreaterThan'), $output);

		return '' .
		'<script type="application/x-aitsu" src="Code.GeSHi:' . $type . '">' . "\n" .
		$output . "\n" .
		'</script>';
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}