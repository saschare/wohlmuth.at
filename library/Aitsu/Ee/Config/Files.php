<?php


/**
 * Checkbox configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Files.php 16811 2010-06-02 19:10:45Z akm $}
 */

class Aitsu_Ee_Config_Files extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'Checkbox.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset, $pattern = '*') {
		
		$idartlang = Aitsu_Registry :: get()->env->idartlang;
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'serialized';
		
		$keyValuePairs = array();
		$files = Aitsu_Core_File :: getFiles($idartlang, $pattern);
		foreach ($files as $file) {
			$keyValuePairs[$file->filename] = $file->filename;
		}
		$instance->params['keyValuePairs'] = $keyValuePairs;
		
		return $instance->currentValue();
	}
}