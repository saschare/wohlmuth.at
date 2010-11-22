<?php


/**
 * Text configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Date.php 16849 2010-06-04 13:05:25Z akm $}
 */

class Aitsu_Ee_Config_Date extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'Date.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset = '') {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'date';
		
		return $instance->currentValue();
	}
}