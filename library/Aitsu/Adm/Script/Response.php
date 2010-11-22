<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Response.php 18687 2010-09-10 10:05:09Z akm $}
 */

class Aitsu_Adm_Script_Response {

	protected $_status;
	protected $_message;
	protected $_exception;
	protected $_nextStep;

	protected function __construct($message, $status, $exception, $isLast) {

		$this->_message = $message;
		$this->_status = $status;
		$this->_exception = $exception;

		if ($isLast) {
			$this->_nextStep = false;
		}
	}

	public static function factory($message, $status = null, $exception = null, $isLast = false) {

		$instance = new self($message, $status, $exception, $isLast);
		
		return $instance;
	}

	public function setNextStep($nextStep) {

		$this->_nextStep = $nextStep;
	}

	public function toArray() {
		
		if ($this->_exception != null) {
			$this->_message = 'Exception occured with message: ' . $this->_exception->getMessage();
			$this->_message = sprintf(Aitsu_Translate :: translate('Exception occured with message: %s'), $this->_exception->getMessage());
			$this->_status = 'exception';
		}

		return array (
			'status' => $this->_status == null ? 'success' : $this->_status,
			'message' => $this->_message,
			'nextStep' => $this->_nextStep !== false ? $this->_nextStep : ''
		);
	}
}