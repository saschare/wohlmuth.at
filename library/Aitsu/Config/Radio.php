<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 28.01.2011
 * Use class Aitsu_Content_Config_Radio instead.
 */
class Aitsu_Config_Radio extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'radio.phtml';
	}
	
	public static function set($index, $name, $label, $keyValuePairs, $fieldset) {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->params['keyValuePairs'] = $keyValuePairs;
		
		return $instance->currentValue();
	}
}