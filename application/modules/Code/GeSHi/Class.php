<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19958 2010-11-18 20:03:59Z akm $}
 */

class Module_Code_GeSHi_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'GeSHi',
			'description' => Aitsu_Translate :: translate('Generic Syntax Highlighter.'),
			'type' => array (
				'Content',
				'Code'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57ea4-b4c8-42ed-93d9-4c097f000101'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('Code.GeSHi', true);

		$lang = $context['index'];
		$code = $context['params'];

		$id = md5($lang . $code);

		$output = '';
		if ($instance->_get('Geshi_' . $id, $output)) {
			return $output;
		}

		$output = Aitsu_GeSHi :: parse($code, $lang);

		$instance->_save($output, 'eternal');

		return $output;
	}
}