<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 28.01.2011
 * Currently there is no replacement available. Beginning with version 2.1.0
 * this class will no longer be usable. It is available for documentation
 * purposes only. 
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

		$return = array ();

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