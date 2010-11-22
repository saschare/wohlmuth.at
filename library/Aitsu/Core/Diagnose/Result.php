<?php


/**
 * aitsu Diagnose result.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Result.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Core_Diagnose_Result {
	
	const STATUS_OK = 0;
	const STATUS_WARNING = 1;
	const STATUS_CRITICAL = 2;
	const STATUS_ABORT = 99;

	protected $status = null;
	protected $message = '';
	protected $title = '';
	protected $allowFix = false;
	
	public function __construct($status, $title, $message, $allowFix = false) {
		
		$this->status = $status;
		$this->title = $title;
		$this->message = $message;
		$this->allowFix = $allowFix;
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function isFix() {
		return $this->allowFix;
	}
}