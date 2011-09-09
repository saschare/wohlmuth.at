<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_Date {

	protected $_time = null;

	public function __construct($time = null) {

		$this->_time = $time;
	}

	public function getTime() {

		return $this->_time;
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

	public static function from($date) {

		return new self(strtotime($date));
	}

	public function add($seconds) {

		$newTime = $this->_time + $seconds;

		$before = date('I', $this->_time);
		$after = date('I', $newTime);

		if ($before > $after) {
			$newTime = $newTime +60 * 60;
		}
		elseif ($before < $after) {
			$newTime = $newTime -60 * 60;
		}

		$this->_time = $newTime;

		return $this;
	}

	public function getStartOfDay() {

		$currentDate = getDate($this->_time);
		return mktime(0, 0, 0, $currentDate['mon'], $currentDate['mday'], $currentDate['year']);
	}

	public static function dayOfCurrentWeek($day) {

		$day = $day == 0 ? 6 : $day -1;

		$currentDate = getDate();
		$currentWeekDay = $currentDate['wday'] - 1;

		$newDay = $day - $currentWeekDay;

		return new self(mktime(0, 0, 0, $currentDate['mon'], $currentDate['mday'], $currentDate['year']) + $newDay * 24 * 60 * 60);
	}
}