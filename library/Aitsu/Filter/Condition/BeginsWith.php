<?php


/**
 * String begins with...
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: BeginsWith.php 15946 2010-04-16 15:11:01Z akm $}
 */

class Aitsu_Filter_Condition_BeginsWith implements Aitsu_Filter_Condition_Interface {

	protected $member = '';
	protected $text = '';

	public function __construct($member, $text) {

		$this->member = $member;

		if (is_array($text)) {
			$this->text = $text;
		} else {
			$this->text = array (
				$text
			);
		}
	}

	public function isTrue(& $value) {

		$checkValue = strtolower($value-> {
			$this->member });

		foreach ($this->text as $text) {
			if (substr($checkValue, 0, strlen($text)) == $text) {
				return true;
			}
		}

		return false;
	}
}