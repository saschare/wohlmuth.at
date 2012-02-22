<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Event extends Aitsu_Event_Abstract {
	
	public static function raise($signature, $context) {
		
		new self($signature, $context);
	}
}