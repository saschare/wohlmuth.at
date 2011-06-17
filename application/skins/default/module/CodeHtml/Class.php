<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_CodeHtml_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'CodeHtml',
			'description' => Aitsu_Translate :: translate('Styles HTML Code'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db93943-c380-430d-bb80-039b50431368'
		);
	}

	public static function init($context) {

		$instance = new self();
		$index = empty ($context['index']) ? 'noindex' : $context['index'];

		$output = '';
		if ($instance->_get('CodeHtml_' . $context['index'], $output)) {
			return $output;
		}

		$text = Aitsu_Content_Text :: get($index, 0);

		$text = (empty ($text) && Aitsu_Application_Status :: isEdit()) ? '<html>....' : $text;

		$output = '<script type="application/x-aitsu" src="Code.GeSHi:html4strict">' . $text . '</script>';
		
		$instance->_save($output, 'eternal');

		return $output;
	}

}