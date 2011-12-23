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
		
		$errfile = substr($errfile, strlen(realpath(APPLICATION_PATH . '/../')));
		
		$errorHandlerMap = array(
			E_COMPILE_ERROR	  => Zend_Log::CRIT,
			E_CORE_ERROR        => Zend_Log::CRIT,
			E_RECOVERABLE_ERROR => Zend_Log::CRIT,			
			E_WARNING           => Zend_Log::WARN,
			E_CORE_WARNING      => Zend_Log::WARN,
			E_COMPILE_WARNING   => Zend_Log::WARN,
			E_USER_WARNING      => Zend_Log::WARN,
			E_ERROR             => Zend_Log::ERR,
			E_USER_ERROR        => Zend_Log::ERR,
			E_STRICT            => Zend_Log::DEBUG,
			E_NOTICE            => Zend_Log::NOTICE,
			E_USER_NOTICE       => Zend_Log::NOTICE,			
		);
      
      // PHP 5.3.0+
		if (defined('E_DEPRECATED')) {
			$errorHandlerMap['E_DEPRECATED'] = Zend_Log::DEBUG;
		}
		if (defined('E_USER_DEPRECATED')) {
			$errorHandlerMap['E_USER_DEPRECATED'] = Zend_Log::DEBUG;
		}	
        

		$level = (isset($errorHandlerMap[$errno])) ? $errorHandlerMap[$errno] : Zend_Log :: INFO;
		
		self :: log($errstr . '[Thrown @:' . $errfile . ':' . $errline . ']', $level);
	}
}