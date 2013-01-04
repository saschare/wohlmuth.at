<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Service_Google_Analytics_Event {

	protected $_data = array ();
	protected $_eventId = -1;

	private function __construct() {
	}

	protected static function getInstance($doNotCount = false) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		if (!$doNotCount) {
			$instance->_eventId += 1;
		}

		return $instance;
	}

	public static function add($category, $action, $label, $value, $nonInteraction = false) {

		$instance = Aitsu_Service_Google_Analytics_Event :: getInstance();

		$instance->_data[$instance->_eventId]['category'] = $category;
		$instance->_data[$instance->_eventId]['action'] = $action;
		$instance->_data[$instance->_eventId]['label'] = $label;
		$instance->_data[$instance->_eventId]['value'] = preg_replace('/[^0-9]/', '', $value);
		$instance->_data[$instance->_eventId]['nonInteraction'] = $nonInteraction ? 'true' : 'false';

		return $instance;
	}

	public static function getPush() {

		$instance = Aitsu_Service_Google_Analytics_Event :: getInstance(true);

		if (empty ($instance->_data)) {
			return '';
		}
		
		var_dump($instance->_data);

		array_walk_recursive($instance->_data, array (
			$instance,
			'_escapeForJs'
		));

		$return = '';

		foreach ($instance->_data as $key => $val) {
			$return .= "\t" . '_gaq.push([\'_trackEvent\',\'';
			$return .= implode('\',\'', $instance->_data[$key]);
			$return .= '\']);' . "\n";
		}

		return $return;
	}

	protected function _escapeForJs(& $val) {

		if (is_object($val) && get_class($val) == 'Aitsu_Service_Google_Analytics_Javascript') {
			/*
			 * Do not escape characters.
			 */
			$val = $val->toString();
		} else {
			$val = preg_replace("/\r?\n/", "\\n", addslashes($val));
		}
	}
}