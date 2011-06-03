<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class LogsPluginController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public function indexAction() {

		header("Content-type: text/javascript");
	}

	public function storeAction() {

		$data = array ();

		$logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';

		if (file_exists($logFile) && is_readable($logFile)) {
			$log = file($logFile, FILE_IGNORE_NEW_LINES);
			foreach ($log as $entry) {
				if (preg_match('/(\\d{4})-(\\d{2})-(\\d{2}).(\\d{2}):(\\d{2}):(\\d{2})[^\\s]*\\s*([^:]*):\\s*(.*)/', $entry, $match)) {
					$data[] = (object) array (
						'time' => "{$match[4]}:{$match[5]}:{$match[6]}",
						'type' => $match[7],
						'entry' => htmlentities((substr($match[8], 0, 200))),
						'full' => (str_replace("\n", '', $match[8]))
					);
				} else {
					$data[count($data) - 1]->full .= "\n" . $entry;
				}
				if (count($data) > 100) {
					array_shift($data);
				}
			}
		}

		$this->_helper->json((object) array (
			'data' => array_reverse($data)
		));
	}

	public function deleteAction() {

		$logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';

		if (file_exists($logFile) && is_readable($logFile)) {
			unlink($logFile);
		}

		$this->_helper->json((object) array (
			'success' => true
		));
	}
}