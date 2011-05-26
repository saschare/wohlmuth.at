<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 *
 */

class Skin_Module_File_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'File',
			'description' => Aitsu_Translate :: translate('Inserts a link to a file'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Frank Ammari',
				'copyright' => 'Ammari & Ammari GbR'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ddcd2d7-a068-484f-a4eb-259a50431bca'
		);
	}
	
	public static function init($context) {

		$index = $context['index'];

		$instance = new self();

		$files = Aitsu_Content_Config_Media :: set($index, 'FileMedia', 'File');
		$files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);

        $value = Aitsu_Content_Config_Text :: set($index, 'FileValue', 'Value', 'Link');
		
        $id = Aitsu_Content_Config_Text :: set($index, 'FileId', 'id', 'Additional attributes');
        $class = Aitsu_Content_Config_Text :: set($index, 'FileClass', 'class', 'Additional attributes');
        $style = Aitsu_Content_Config_Text :: set($index, 'FileStyle', 'style', 'Additional attributes');

		$view = $instance->_getView();
		
		if (!$files) {
			if (Aitsu_Application_Status :: isEdit()) {
				return '| File :: ' . $index . ' |';
			} else {
				return '';
			}
		}

		$view->index = $index;
		$view->files = $files;
		$view->value = $value;
		
		$view->id = empty($id) ? NULL : ' id="' . $id . '"';
		$view->class = empty($class) ? NULL : ' class="' . $class . '"';
		$view->style = empty($style) ? NULL : ' style="' . $style . '"';
		
		$output = $view->render('index.phtml');

		return $output;
	}
}