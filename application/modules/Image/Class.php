<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Image_Class extends Aitsu_Module_Abstract {

	protected function _main() {

		$template = empty ($this->_params->template) ? 'index' : $this->_params->template;
		$images = Aitsu_Content_Config_Media :: set($this->_index, 'Image.Media', 'Media');
		$template = Aitsu_Content_Config_Radio :: set($this->_index, 'Image.Template', '', $this->_getTemplates(), 'Template');

		if (empty ($template) || empty ($images) || !in_array($template, $this->_getTemplates())) {
			return '';
		}

		$view = $this->_getView();
		$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);

		return $view->render($template . '.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}