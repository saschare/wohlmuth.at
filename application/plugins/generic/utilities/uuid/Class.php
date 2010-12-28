<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class UuidPluginController extends Aitsu_Adm_Plugin_Controller {
	
	public function init() {
		
		$this->_helper->layout->disableLayout();
	}

	public function indexAction() {

		header("Content-type: text/javascript");
	}

}