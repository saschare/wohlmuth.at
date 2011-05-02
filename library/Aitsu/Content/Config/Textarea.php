<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Content_Config_Textarea extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'textarea.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset) {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		
		return $instance->currentValue();
	}
}