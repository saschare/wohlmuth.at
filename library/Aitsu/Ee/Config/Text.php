<?php


/**
 * Text configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Text.php 16750 2010-06-02 08:15:42Z akm $}
 */

class Aitsu_Ee_Config_Text extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'Text.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset) {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		
		return $instance->currentValue();
	}
}