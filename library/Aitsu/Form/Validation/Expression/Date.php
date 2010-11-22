<?php


/**
 * Date.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Date.php 15627 2010-03-26 17:02:32Z akm $}
 */

class Aitsu_Form_Validation_Expression_Date implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {
		
		$value = trim($value);
		
		if (!isset($this->args['format'])) {
			$this->args['format'] = '%d.%m.%Y';
		}

		$dateParts = strptime($value, $this->args['format']);
		$time = mktime($dateParts['tm_hour'], $dateParts['tm_min'], $dateParts['tm_sec'], $dateParts['tm_mon'] + 1, $dateParts['tm_mday'], $dateParts['tm_year'] + 1900);
		$newValue = trim(date(str_replace('%', '', $this->args['format']), $time));
		
		return $value == $newValue;
	}
}