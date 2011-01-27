<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Ee_Module_Image_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();

		$index = $context['index'];
		Aitsu_Content_Edit :: isBlock(false);
		
		$test = Aitsu_Content_Html :: get('Kummer');

		$output = '';
		if (!$instance->_get('Image' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$template = Aitsu_Ee_Config_Radio :: set($index, 'ImageTemplate', '', $instance->_getTemplates(), 'Template');

			if (empty ($template)) {
				$template = 'small';
			}

			$view = $instance->_getView();
			$images = Aitsu_Content_Config_Media :: set($index, 'Image.Media', 'Media');
			$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

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