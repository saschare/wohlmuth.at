<?php


/**
 * Files of a particular source configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: FilesOfPath.php 16863 2010-06-04 16:05:57Z akm $}
 */

class Aitsu_Ee_Config_FilesOfPath extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'Select.phtml';
	}

	public static function set($index, $name, $label, $fieldset, $pattern, $startPath) {

		$idartlang = Aitsu_Registry :: get()->env->idartlang;
		$instance = new self($index, $name);

		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'text';
		
		if (!Aitsu_Registry :: isEdit()) {
			return $instance->currentValue();
		}

		$keyValuePairs = array ();
		
		$files = self :: _glob($startPath, $pattern);
		foreach ($files as $file) {
			$file = substr($file, strlen($startPath));
			$keyValuePairs[$file] = $file;
		}
		$instance->params['keyValuePairs'] = $keyValuePairs;

		return $instance->currentValue();
	}

	protected static function _glob($dir, $pattern) {

		$return = array();

		$items = glob($dir . '/*');	

		foreach ($items as $item) {
			if (is_dir($item)) {
				$return = array_merge($return, self :: _glob($item . '/*', $pattern));
			} else {
				if (preg_match($pattern, $item)) {
					$return[] = $item;
				}
			}
		}

		return $return;
	}
}