<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18285 2010-08-23 13:11:23Z akm $}
 */

class Aitsu_Ee_Module_GeSHi_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'GeSHi',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Generic Syntax Highlighter.'),
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
			'id' => 'a0725368-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('GeSHi', true);

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