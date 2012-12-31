<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Util_Date {

	protected $_time = null;

	/**
	 * Constructor.
	 * 
	 * @param Integer Timestamp. Null, if current timestamp has to be used.
	 */
	public function __construct($time = null) {

		$this->_time = $time == null ? time() : $time;
	}

	/**
	 * Returns the timestamp.
	 * 
	 * @param Void 
	 * @return Integer Timestamp.
	 */
	public function getTime() {

		return $this->_time;
	}

	/**
	 * Returns a date util object based on the given MySQL date.
	 * 
	 * @param String MySQL date.
	 * @return Aitsu_Util_Date Date based on the given MySQL date.
	 */
	public static function fromMySQL($date) {

		if (!preg_match('/(\\d{4})\\-(\\d{2})\\-(\\d{2})\\s*(\\d{2})\\:(\\d{2})\\:(\\d{2})/', $date, $match)) {
			return new self();
		}

		return new self(mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]));
	}

	/**
	 * Returns a string representation of the current object.
	 * 
	 * @param String Date format. Defaults to d.m.y H:i.
	 * @return String Date representation in the given format.
	 */
	public function get($format = 'd.m.y H:i') {

		return date($format, $this->_time);
	}

	/**
	 * Returns a date util object based on the given string.
	 * 
	 * @param String Date string.
	 * @return Aitsu_Util_Date Date based on the given string.
	 */
	public static function from($date) {

		return new self(strtotime($date));
	}

	/**
	 * Adds the given seconds to the current value of the date object.
	 * 
	 * @param Integer Seconds to be added.
	 * @return Aitsu_Util_Date The current object.
	 */
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

	/**
	 * Returns the start of the day of the current object's value.
	 * 
	 * @param Void 
	 * @return Integer Timestamp of the start of the day.
	 */
	public function getStartOfDay() {

		$currentDate = getDate($this->_time);
		return mktime(0, 0, 0, $currentDate['mon'], $currentDate['mday'], $currentDate['year']);
	}

	/**
	 * Returns the specified day of the current week as a date util object.
	 * 
	 * @param Integer Day.
	 * @return Aitsu_Util_Date Date object.
	 */
	public static function dayOfCurrentWeek($day) {

		$day = $day == 0 ? 6 : $day -1;

		$currentDate = getDate();
		$currentWeekDay = $currentDate['wday'] - 1;

		$newDay = $day - $currentWeekDay;

		return new self(mktime(0, 0, 0, $currentDate['mon'], $currentDate['mday'], $currentDate['year']) + $newDay * 24 * 60 * 60);
	}

	/**
	 * Returns the number of seconds until the end of the current
	 * year, month, day or week.
	 * 
	 * @param String Type. Either year, month, day or week.
	 * @return Integer Seconds until the end of the specified period.
	 */
	public static function secondsUntilEndOf($type) {

		$now = time();

		$year = date('Y');

		if (strtolower($type) == 'year') {
			return mktime(0, 0, 0, 1, 1, $year +1) - $now;
		}

		$month = date('n');

		if (strtolower($type) == 'month') {
			return mktime(0, 0, 0, $month +1, 1, $year) - $now;
		}

		$day = date('j');

		if (strtolower($type) == 'day') {
			return mktime(0, 0, 0, $month, $day +1, $year) - $now;
		}

		$weekday = date('w');
		$weekday = $weekday == 0 ? 7 : $weekday; // Make 7 to represent sunday.

		return mktime(0, 0, 0, $month, $day +8 - $weekday, $year) - $now;
	}
}