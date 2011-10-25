<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_List_Download_Class extends Aitsu_Module_Abstract {

	public static function _main() {

		$view = $this->_getView();

		$files = Aitsu_Content_Config_Media :: set($this->_index, 'DownloadList', 'Files');
		$view->files = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $files);

		return $view->render('index.phtml');
	}
	
	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}