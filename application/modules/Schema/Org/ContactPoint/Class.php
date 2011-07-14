<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
 
include_once (APPLICATION_PATH . '/modules/Schema/Org/StructuredValue/Class.php');

class Module_Schema_Org_ContactPoint_Class extends Module_Schema_Org_StructuredValue_Class {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();
		
		return $view->render('index.phtml');
	}
	
	protected function _getView() {
		
		$view = parent :: _getView();
		
		$view->contactType = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.ContactPoint.ContactType', 'Type', 'ContactPoint');
		$view->email = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.ContactPoint.Email', 'Email', 'ContactPoint');
		$view->faxnumber = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.ContactPoint.Faxnumber', 'Fax', 'ContactPoint');
		$view->telefone = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.ContactPoint.Telefone', 'Phone', 'ContactPoint');
		
		return $view;
	}
}