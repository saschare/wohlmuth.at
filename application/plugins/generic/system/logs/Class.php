<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class LogsPluginController extends Aitsu_Adm_Plugin_Controller {
	
	protected $_logFile;
	
	public function init() {
		
		$this->_helper->layout->disableLayout();
		
		$this->_logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';
	}

	public function indexAction() {
		
		header("Content-type: text/javascript");
	}

	public function deleteAction() {
		
		$this->_helper->viewRenderer->setNoRender(true);
		
		if (file_exists($this->_logFile) && is_readable($this->_logFile)) {
			unlink($this->_logFile);
		}
	}
	
	public function refreshAction() {
		
		if (file_exists($this->_logFile) && is_readable($this->_logFile)) {
			$this->view->content = file_get_contents($this->_logFile);
		}
	}
}