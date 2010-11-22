<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Date.php 18277 2010-08-23 11:37:19Z akm $}
 */

class Aitsu_Util_Date {

	protected $_time = null;

	public function __construct($time = null) {

		$this->_time = $time;
	}

	public static function fromMySQL($date) {

		if (!preg_match('/(\\d{4})\\-(\\d{2})\\-(\\d{2})\\s*(\\d{2})\\:(\\d{2})\\:(\\d{2})/', $date, $match)) {
			return new self();
		}

		return new self(mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]));
	}

	public function get($format = 'd.m.y H:i') {

		return date($format, $this->_time);
	}
}