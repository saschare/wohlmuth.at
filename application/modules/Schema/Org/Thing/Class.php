<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Schema_Org_Thing_Class extends Aitsu_Module_Abstract {
	
	protected function _init() {}
	
	protected function _main() {
		
		$view = $this->_getView();
		
		$view->description = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.Thing.Description', 'Description', 'Thing');
		$images = Aitsu_Content_Config_Media :: set($this->_index, 'schema.org.Image', 'Image');
		$view->images = Aitsu_Persistence_View_Media :: byFileName(Aitsu_Registry :: get()->env->idart, $images);
		$view->name = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.Name', 'Name', 'Thing');
		$view->url = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.URL', 'URL', 'Thing');
		
		if (empty($view->description) && Aitsu_Application_Status :: isEdit()) {
			$view->description = 'DESCRIPTION';
		}
		
		return $view->render('index.phtml');
	}
}