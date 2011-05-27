<?php


/**
 * aitsu profiler.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Profiler.php 17827 2010-07-29 13:11:30Z akm $}
 */

class Aitsu_Profiler {

	protected $profilerStart;
	protected $active = false;
	protected $profiles = array ();
	protected $currentProfilings = array ();

	protected function __construct() {

		if (!isset ($_GET['profile'])) {
			return;
		}

		$this->profilerStart = microtime(true);

		if (isset (Aitsu_Registry :: get()->config->admin->allow->profiling) && Aitsu_Registry :: get()->config->admin->allow->profiling) {
			/*
			 * Disable caching on page level.
			 */
			Aitsu_Ee_Cache_Page :: lifetime(-1);

			/*
			 * Activate profiling.
			 */
			$this->active = true;
		}
	}

	protected static function _getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function profile($token, $data = null, $type = 'module') {

		$time = microtime(true);

		$instance = self :: _getInstance();

		if (!$instance->active) {
			return;
		}

		if (!isset ($instance->currentProfilings[$type][$token])) {
			$instance->currentProfilings[$type][$token] = array (
				'start' => $time,
				'end' => null,
				'data' => $data,
				'token' => $token
			);
			return;
		}

		$pf = $instance->currentProfilings[$type][$token];

		$pf['end'] = $time;
		$pf['period'] = ($pf['end'] - $pf['start']) * 1000;

		if ($data != null) {
			$pf['data'] = $data;
		}

		$instance->profiles[$type][] = (object) $pf;

		unset ($instance->currentProfilings[$type][$token]);
	}

	public static function get() {

		$end = microtime(true);

		$instance = self :: _getInstance();

		if (!$instance->active) {
			return false;
		}

		if (defined('REQUEST_START')) {
			$overHead = (object) array (
				'start' => REQUEST_START,
				'end' => $instance->profilerStart,
				'period' => ($instance->profilerStart - REQUEST_START) * 1000,
				'token' => 'AutoloadAndConfig'
			);
			array_unshift($instance->profiles['system'], $overHead);
		}

		$aggregation = array ();
		foreach ($instance->profiles as $type => $profiles) {
			$sum = 0;
			foreach ($profiles as $profile) {
				$sum += $profile->period;
			}
			$aggregation[$type] = (object) array (
				'period' => $sum
			);
		}
		$instance->profiles['type.sum'] = $aggregation;

		if (defined('REQUEST_START')) {
			$instance->profiles['type.sum']['totalResponse'] = (object) array (
				'period' => ($end -REQUEST_START) * 1000
			);
		}

		return $instance->_getView('index.phtml');
	}
	
	protected function _getView($template = 'index.phtml') {
		
		$view = new Zend_View();
		$view->setScriptPath(realpath(APPLICATION_PATH . '/../library/Aitsu/Profiler'));
		
		$view->profiles = $this->profiles;
		
		return $view->render($template);
	}
}