<?php


/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Image_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$template = empty ($this->_params->template) ? 'index' : $this->_params->template;

		$images = Aitsu_Content_Config_Media :: set($this->_index, 'media', 'Media');

		$view = $this->_getView();
		$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

		$output = $view->render($template . '.phtml');

		return $output;
	}

}