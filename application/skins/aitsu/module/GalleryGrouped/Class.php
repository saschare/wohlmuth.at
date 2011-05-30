<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 * @sponsor Felix Kuster, fashionweb.ch 
 */

class Skin_Module_GalleryGrouped_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'GalleryGrouped',
			'description' => Aitsu_Translate :: translate('Returns a grouped image gallery'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.1',
			'status' => 'stable',
			'url' => null,
			'id' => '4ddea821-aabc-42ce-8f4a-1ff47f000001'
		);
	}
	
	public static function init($context) {

		$instance = new self();

		$index = $context['index'];

		$output = '';
		if (!$instance->_get('GalleryGrouped' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$view = $instance->_getView();

			$images = Aitsu_Content_Config_Media :: set($index, 'GalleryMedia', 'Media');
			$images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);
			$modulo = Aitsu_Content_Config_Text :: set($index, 'GalleryModulo', 'Modulo', 'Grouping');
			
			$view->index = $index;
			$view->images = $images;
			$view->modulo = $modulo;

			if (count($view->images) == 0) {
				if (Aitsu_Application_Status :: isEdit()) {
					$output = '| GalleryGrouped :: ' . $index . ' |';
				} else {
					$output = '';
				}
			} else {
				$output = $view->render('index.phtml');
				$instance->_save($output, 'eternal');
			}
		}

		return $output;
	}
}