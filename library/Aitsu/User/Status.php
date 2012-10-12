<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */

class Aitsu_User_Status {

	static protected $_instance = null;
	protected $_status = array ();

	protected function __construct() {

		if (empty (Aitsu_Registry :: get()->session->UserStatus)) {
			Aitsu_Registry :: get()->session->UserStatus = array ();
		}

		$this->_status = & Aitsu_Registry :: get()->session->UserStatus;
	}

	protected static function _getInstance() {

		if (empty (self :: $_instance)) {
			self :: $_instance = new self();
		}

		return self :: $_instance;
	}

	public static function isHuman($set = null) {

		$instance = self :: _getInstance();

		if (!is_null($set)) {
			$instance->_status['human'] = (boolean) $set;
		}
		elseif (!isset ($instance->_status['human'])) {
			$instance->_status['human'] = false;
		}

		return $instance->_status['human'];
	}
	
	public static function pageStack($set = null) {
		
		$instance = self :: _getInstance();
		
		if (empty($instance->_status['pageStack'])) {
			$instance->_status['pageStack'] = array();
		}
		
		if (is_null($set)) {
			return $instance->_status['pageStack'];
		}
	
		if (self :: getUrl(0) == $set) {
			/*
			 * We ignore the entry as it is the same as the last page
			 */
			return;
		}

		array_unshift($instance->_status['pageStack'], $set);
		
		if (count($instance->_status['pageStack']) > 20) {
			array_pop($instance->_status['pageStack']);
		}
	}
	
	public static function getUrl($index = 1) {
		
		$instance = self :: _getInstance();
		
		if (empty($instance->_status['pageStack']) || count($instance->_status['pageStack']) < $index + 1) {
			return null;
		}
		
		return $instance->_status['pageStack'][$index];
	}
}