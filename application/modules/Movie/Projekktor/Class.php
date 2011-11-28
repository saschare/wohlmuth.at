<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Movie_Projekktor_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {
		
		if (Aitsu_Application_Status :: isEdit()) {
			return;
		}

		$mainDir = Aitsu_Config :: get('sys.mainDir');
		Aitsu_Util_Javascript :: addReference($mainDir . 'res/jQuery/1.6.2/jquery.min.js');
		Aitsu_Util_Javascript :: addReference($mainDir . 'res/video/projekktor/projekktor.min.js');

		Aitsu_Util_Javascript :: add($this->_getView()->render('js.phtml'));
	}

	protected function _main() {

		$view = $this->_getView();

		$view->dir = Aitsu_Config :: get('sys.mainDir');
		$view->title = Aitsu_Content_Config_Text :: set($this->_index, 'Projekktor.Title', 'Title', 'Movie');
		$view->file = Aitsu_Content_Config_Text :: set($this->_index, 'Projekktor.File', 'File', 'Movie');
		$view->width = Aitsu_Content_Config_Text :: set($this->_index, 'Projekktor.Width', 'Width', 'Dimension');
		$view->height = Aitsu_Content_Config_Text :: set($this->_index, 'Projekktor.Height', 'Height', 'Dimension');

		if (Aitsu_Application_Status :: isEdit() && empty ($view->file)) {
			return '[[ :: Please configure the projekktor. :: ]]';
		}

		return $view->render('index.phtml');
	}

	public static function help() {
		
		$instance = new Module_Movie_Projekktor_Class();
		
		return $instance->_getView()->render('help.phtml');
	}
}