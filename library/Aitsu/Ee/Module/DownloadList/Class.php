<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

class Aitsu_Ee_Module_DownloadList_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function init($context) {

		$instance = new self();
		$index = $context['index'];

		$output = '';
		if ($instance->_get('DownloadList_' . $index, $output)) {
			return $output;
		}

		$files = Aitsu_Content_Config_Media :: set($index, 'DownloadListElements', 'Files');
		$files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);

		$view = $instance->_getView();
		$view->files = $files;

		$output = $view->render('index.phtml');
		$instance->_save($output, 'eternal');

		return $output;
	}
}