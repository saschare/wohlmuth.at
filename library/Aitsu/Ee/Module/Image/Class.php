<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_Image_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();

		$index = $context['index'];
		Aitsu_Content_Edit :: isBlock(false);

		$output = '';
		if (!$instance->_get('Image' . preg_replace('/[^a-zA-Z_0-9]/', '', $index), $output)) {

			$template = Aitsu_Content_Config_Radio :: set($index, 'ImageTemplate', '', $instance->_getTemplates(), 'Template');

			if (empty ($template)) {
				$template = 'small';
			}

			$view = $instance->_getView();
			$images = Aitsu_Content_Config_Media :: set($index, 'Image_Media', 'Media');
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