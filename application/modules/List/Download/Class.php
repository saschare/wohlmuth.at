<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_List_Download_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();
		$index = $context['index'];

		$output = '';
		if ($instance->_get('List_Download' . $index, $output)) {
			return $output;
		}

		$view = $instance->_getView();

		$files = Aitsu_Content_Config_Media :: set($index, 'DownloadList', 'Files');
		$view->files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);

		$output = $view->render('index.phtml');
		$instance->_save($output, 'eternal');

		return $output;
	}
}