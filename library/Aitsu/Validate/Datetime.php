<?php


/**
 * Validates a date string optionally with a time part.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Datetime.php 18433 2010-08-30 12:46:00Z akm $}
 */

class Aitsu_Validate_Datetime extends Zend_Validate_Abstract {

	const DATETIME = 'datetime';

	protected $_messageTemplates = array (
		self :: DATETIME => "'%value%' is not a valid date"
	);

	public function isValid($value) {

		$this->_setValue($value);

		if (!$this->_isValidDate($value)) {
			$this->_error();
			return false;
		}

		return true;
	}

	protected function _isValidDAte($value) {

		if (preg_match('/^(\\d{4})\\-(\\d{2})\\-(\\d{2})$/', $value, $match)) {
			$time = mktime(0, 0, 0, $match[2], $match[3], $match[1]);
			$date = date('Y-m-d', $time);
		}
		elseif (preg_match('/^(\\d{4})\\-(\\d{2})\\-(\\d{2})\\s(\\d{2})\\:(\\d{2}):(\\d{2})$/', $value, $match)) {
			$time = mktime($match[4], $match[5], $match[6], $match[2], $match[3], $match[1]);
			$date = date('Y-m-d H:i:s', $time);
		} else {
			return false;
		}
		
		return $date == $value;
	}
}
?>
