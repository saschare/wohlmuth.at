<?php

/**
 * @author Frank Ammari, meine experten GbR
 * @copyright Copyright &copy; 2010, meine experten GbR
 */

class Skin_Module_CodePhp_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'CodePhp',
			'description' => Aitsu_Translate :: translate('Styles PHP Code'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'meine experten GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db93974-1e90-4e1b-b96c-03c250431368'
		);
	}

	public static function init($context) {

		$instance = new self();
		$index = empty ($context['index']) ? 'noindex' : $context['index'];

		$output = '';
		if ($instance->_get('CodePhp_' . $context['index'], $output)) {
			return $output;
		}

		$text = Aitsu_Content_Text :: get($index, 0);

		$text = (empty ($text) && Aitsu_Registry :: isEdit()) ? '<?php ....' : $text;

		$output = '<script type="application/x-aitsu" src="Code.GeSHi:php">' . $text . '</script>';
		
		$instance->_save($output, 'eternal');

		return $output;
	}

}