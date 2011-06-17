<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_CodeIni_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'CodeIni',
			'description' => Aitsu_Translate :: translate('Styles INI Code'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db9395a-0f50-4c15-9395-03b150431368'
		);
	}

	public static function init($context) {

		$instance = new self();
		$index = empty ($context['index']) ? 'noindex' : $context['index'];

		$output = '';
		if ($instance->_get('CodeIni_' . $context['index'], $output)) {
			return $output;
		}

		$text = Aitsu_Content_Text :: get($index, 0);

		$text = (empty ($text) && Aitsu_Application_Status :: isEdit()) ? '#ini ....' : $text;

		$output = '<script type="application/x-aitsu" src="Code.GeSHi:ini">' . $text . '</script>';
		
		$instance->_save($output, 'eternal');

		return $output;
	}

}