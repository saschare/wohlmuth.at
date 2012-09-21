<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 28.01.2011
 * Please use class Aitsu_Content_Config_Media instead.
 */
class Aitsu_Config_Files extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'Checkbox.phtml';
	}

	public static function set($index, $name, $label, $fieldset, $pattern = '*') {

		$idartlang = Aitsu_Registry :: get()->env->idartlang;
		$instance = new self($index, $name);

		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'serialized';

		$keyValuePairs = array ();
		$files = Aitsu_Core_File :: getFiles($idartlang, $pattern);
		foreach ($files as $file) {
			$keyValuePairs[$file->filename] = $file->filename;
		}
		$instance->params['keyValuePairs'] = $keyValuePairs;

		return $instance->currentValue();
	}
}