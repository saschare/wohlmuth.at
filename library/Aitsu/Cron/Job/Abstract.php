<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

abstract class Aitsu_Cron_Job_Abstract {

	abstract protected function _exec();

	abstract protected function _isPending($lasttime);

	public function execute($job) {
		
		if ($this->_isPending($this->_getLasttime($job))) {
			$this->_logDone($job);
			$this->_exec();
		}
	}

	private function _getLasttime($job) {

		$logDir = APPLICATION_PATH . '/data/cron/';

		if (!is_dir($logDir)) {
			mkdir($logDir, 0777, true);
		}
		
		if (!file_exists($logDir . $job)) {
			return 0;
		}
		
		$data = file($logDir . $job);
		
		return $data[0];
	}
	
	private function _logDone($job) {
		
		$logDir = APPLICATION_PATH . '/data/cron/';
		
		if (!file_exists($logDir . $job)) {
			file_put_contents($logDir . $job, time());
			return;
		}
		
		$data = file($logDir . $job, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		array_unshift($data, time());
		$data = array_slice ($data , 0, 100);
		file_put_contents($logDir . $job, implode("\n", $data));
	}
}