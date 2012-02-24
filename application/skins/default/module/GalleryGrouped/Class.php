<?php


/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 * @sponsor Felix Kuster, fashionweb.ch 
 */

class Skin_Module_GalleryGrouped_Class extends Aitsu_Module_Abstract {

	protected function _main() {

		$view = $this->_getView();

		$images = Aitsu_Content_Config_Media :: set($this->_index, 'GalleryMedia', 'Media');
		$images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);
		$modulo = Aitsu_Content_Config_Text :: set($this->_index, 'GalleryModulo', 'Modulo', 'Grouping');

		$view->index = $this->_index;
		$view->images = $images;
		$view->modulo = $modulo;

		if (count($view->images) == 0) {
			return '';
		}

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}