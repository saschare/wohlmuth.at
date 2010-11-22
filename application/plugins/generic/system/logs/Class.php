<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18915 2010-09-21 10:41:39Z akm $}
 */

class LogsPluginController extends Aitsu_Adm_Plugin_Controller {
	
	protected $_logFile;
	
	public function init() {
		
		$this->_logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';
	}

	public function indexAction() {
		
	}

	public function deleteAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if (file_exists($this->_logFile) && is_readable($this->_logFile)) {
			unlink($this->_logFile);
		}
	}
	
	public function refreshAction() {
		
		$this->_helper->layout->disableLayout();
		
		if (file_exists($this->_logFile) && is_readable($this->_logFile)) {
			$this->view->content = file_get_contents($this->_logFile);
		}
	}
}