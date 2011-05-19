<?php

/**
 * @author Frank Ammari, meine experten GbR
 * @copyright Copyright &copy; 2011, meine experten GbR
 *
 */

class Skin_Module_Flash_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Flash',
			'description' => Aitsu_Translate :: translate('Inserts a flash movie'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'meine experten GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4db93dc0-1d54-47ef-a119-08c050431368'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();

		$files = Aitsu_Content_Config_Media :: set($index, 'FlashMedia', 'Flash file');
		$files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);

		$images = Aitsu_Content_Config_Media :: set($index, 'FlashImageMedia', 'Alternative image');
		$images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

		$width = Aitsu_Content_Config_Text :: set($index, 'FlashWidth', 'width', 'Additional Flash attributes');
		$height = Aitsu_Content_Config_Text :: set($index, 'FlashHeight', 'height', 'Additional Flash attributes');
			
		$view = $instance->_getView();
		$view->index = $index;
		$view->files = $files;
		$view->images = $images;
		$view->width = empty($width) ? 1280 : $width;
		$view->height = empty($height) ? 1280 : $height;

		if (!$files) {
			if (Aitsu_Registry :: get()->env->edit == '1') {
				return '// Flash ' . $index . ' //';
			} else {
				return '';
			}
		}

		$output = $view->render('index.phtml');

		return $output;
	}
}