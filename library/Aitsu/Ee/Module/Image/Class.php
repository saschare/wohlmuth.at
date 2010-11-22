<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18287 2010-08-23 13:11:54Z akm $}
 */

class Aitsu_Ee_Module_Image_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Image',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Standard image module. It returns the specified image(s) rendered with the specified template.'),
			'type' => array (
				'Content',
				'Image'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536d-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();

		$index = $context['index'];
		Aitsu_Content_Edit :: isBlock(false);

		$output = '';
		if (!$instance->_get('Image' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$template = Aitsu_Ee_Config_Radio :: set($index, 'ImageTemplate', '', $instance->_getTemplates(), 'Template');

			if (empty ($template)) {
				$template = 'small';
			}

			$view = $instance->_getView();
			$view->images = Aitsu_Ee_Config_Images :: set($index, 'Image', '', 'Choose image');

			if (count($view->images) == 0) {
				if (Aitsu_Registry :: isEdit()) {
					$output = '// Image ' . $index . ' //';
				} else {
					$output = '';
				}
			} else {
				$output = $view->render($template . '.phtml');
				$instance->_save($output, 'eternal');
			}
		}

		return $output;
	}
}