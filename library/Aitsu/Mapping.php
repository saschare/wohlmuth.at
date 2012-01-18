<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Mapping {

	protected $_environment;
	protected $_mapping;

	protected function __construct() {

		$this->_mapping = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');

		$this->_evalMapping();
	}

	protected static function _getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function getIni() {

		return self :: _getInstance()->_environment;
	}

	protected function _evalMapping() {

		foreach ($this->_mapping->item as $rule) {
			if ($this->_evalsTrue($rule->conditions)) {
				$this->_environment = $rule->env;
				return;
			}
		}
	}

	protected function _evalsTrue($conditions) {

		foreach ($conditions as $condition) {
			if (preg_match('/^(\\w+)\\s*\\:\\s*(.*)/', $condition, $match)) {
				if ($match[1] == 'host') {
					if (!fnmatch($match[2], $_SERVER['HTTP_HOST'])) {
						return false;
					}
				}
				elseif ($match[1] == 'url') {
					if (!fnmatch($match[2], $_SERVER['REQUEST_URI'])) {
						return false;
					}
				}
                                elseif ($match[1] == 'device') {
					if (!fnmatch($match[2], $_SERVER['HTTP_USER_AGENT'])) {
						return false;
					}
				}
				elseif ($match[1] == 'delegate') {
					if (!call_user_func(array (
							$match[2],
							'isMet'
						))) {
						return false;
					}
				} else {
					return false;
				}
			}
		}

		return true;
	}
}