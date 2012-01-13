<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Event_Dispatcher {

	protected $_config = null;

	protected function __construct() {

		$this->_readConfig();
	}

	public static function getInstance() {

		$startTime = microtime(true);

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		$period = number_format((microtime(true) - $startTime) * 1000, 2);

		return $instance;
	}

	protected function _readConfig() {

		if (!defined('APPLICATION_PATH')) {
			/*
			 * Application context not available.
			 */
			return;
		}

		$eventsTable = APPLICATION_PATH . '/configs/listeners.ini';

		if (!is_readable($eventsTable)) {
			/*
			 * Events table is not readable.
			 */
			return;
		}

                if (!empty($_SERVER['PHP_FCGI_CHILDREN']) || !empty($_SERVER['FCGI_ROLE'])) {
                    $env = (getenv("REDIRECT_AITSU_ENV") == '' ? 'default' : getenv("REDIRECT_AITSU_ENV"));
                } else {
                    $env = (getenv("AITSU_ENV") == '' ? 'default' : getenv("AITSU_ENV"));
                }

		$this->_config = new Zend_Config_Ini($eventsTable, $env, array (
			'allowModifications' => true
		));
	}

	public function raise(Aitsu_Event_Abstract $event) {

		if ($this->_config == null) {
			/*
			 * Events table is not existing or for any other reason
			 * not available. We trigger an error of type notice
			 * to inform the log about the missing events table
			 * and quit silently.
			 */
			trigger_error('Events table (/application/configs/listeners.ini) is not available. Events will not be raised.');
			return;
		}

		$this->_invoke($event);
	}

	protected function _invoke($event) {

		$startTime = microtime(true);

		$eventsList = array ();
		$sig = $event->getSignature();
		$conf = $this->_config;

		foreach ($sig as $part) {
			if (isset ($conf-> $part)) {
				if (isset ($conf-> $part->listener)) {
					$eventsList = array_merge($eventsList, array_reverse($conf-> $part->listener->toArray()));
				}
				$conf = $conf-> $part;
			} else {
				break;
			}
		}

		$eventsList = array_reverse($eventsList);
		$executionList = array ();
		foreach ($eventsList as $className => $execute) {
			if ($execute) {
				$executionList[] = $className;
			}
		}
		$executionList = array_unique($executionList);

		try {
			foreach ($executionList as $listener) {
				if (array_key_exists('Aitsu_Event_Listener_Interface', class_implements($listener, true))) {
					call_user_func(array (
						$listener,
						'notify'
					), $event);
				}
			}
		} catch (Aitsu_Security_Exception $e) {
			throw $e;
		} catch (Exception $e) {
			trigger_error('Exception: ' . $e->getMessage());
			trigger_error($e->getTraceAsString());
			trigger_error('WARNING: Notification of pending listeners stopped.');
			trigger_error('Affected event: ' . implode('.', $sig));
		}

		$period = number_format((microtime(true) - $startTime) * 1000, 2);
	}

	public function disable($string) {

		$parts = explode('.', $string);
		$config = & $this->_config;

		for ($i = 0; $i < count($parts) - 1; $i++) {
			if (!isset ($config-> $parts[$i])) {
				return false;
			}
			$config = & $config-> $parts[$i];
		}

		$config->$parts[$i] = false;

		return true;
	}
}