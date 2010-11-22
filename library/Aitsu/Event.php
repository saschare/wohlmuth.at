<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Event.php 19482 2010-10-21 15:15:18Z akm $}
 */

class Aitsu_Event extends Aitsu_Event_Abstract {
	
	public static function raise($signature, $context) {
		
		new self($signature, $context);
	}
}