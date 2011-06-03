<?php


/**
 * GeSHi wrapper. Just for the auto load convenience.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: GeSHi.php 17526 2010-07-08 14:11:34Z akm $}
 */

class Aitsu_GeSHi {
	
	protected $geshi = null;

	protected function __construct($source, $language) {

		include_once ('Aitsu/GeSHi/geshi.php');

		$this->geshi = new GeSHi($source, $language);
		$this->geshi->set_overall_class('geshi');
	}

	protected static function _factory($code, $lang) {

		return new self($code, $lang);
	}

	public static function parse($code, $lang) {

		return self :: _factory($code, $lang)->geshi->parse_code();
	}
}