<?php


/**
 * aitsu Logger.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Logger.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Core_Logger {

	protected $doLog = false;
	protected $logger;

	protected function __construct() {

		$logPath = APPLICATION_PATH . '/data/logs';

		if (!file_exists($logPath)) {
			mkdir($logPath, 0777, true);
		}

		$logFile = $logPath . '/' . date('Y-m-d') . '.log';
		$this->logger = new Zend_Log(new Zend_Log_Writer_Stream($logFile));
	}

	protected static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function log($message, $level) {

		$inst = self :: getInstance();

		$inst->logger->log($message, $level);
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
		
		/*
		 * Remove fopen errors raised in Zend/Loader.php on line 164.
		 */
		if ($errline == 164 && substr($errfile, -1 * strlen('Zend/Loader.php')) == 'Zend/Loader.php') {
			return;
		}

		$level = Zend_Log :: INFO;
		if (in_array($errno, array (
				E_CORE_ERROR,
				E_COMPILE_ERROR,
				E_RECOVERABLE_ERROR
			))) {
			$level = Zend_Log :: CRIT;
		}
		elseif (in_array($errno, array (
			E_WARNING,
			E_CORE_WARNING,
			E_COMPILE_WARNING,
			E_USER_WARNING,
			8192, // equals E_DEPRECATED in PHP 5.3.x
			16384 // equals E_USER_DEPRECATED in PHP 5.3.x
		))) {
			$level = Zend_Log :: WARN;
		}
		elseif (in_array($errno, array (
			E_NOTICE,
			E_USER_NOTICE
		))) {
			$level = Zend_Log :: NOTICE;
		}

		self :: log($errstr . ' in ' . $errfile . ' on line ' . $errline, $level);
	}
}