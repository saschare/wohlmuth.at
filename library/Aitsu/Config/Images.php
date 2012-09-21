<?php


/**
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 */

/**
 * @deprecated 2.1.0 - 28.01.2011
 * Please use class Aitsu_Content_Config_Media instead.
 */
class Aitsu_Config_Images extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'Images.phtml';
	}

	public static function set($index, $name, $label, $fieldset, $pattern = '*') {

		$idartlang = Aitsu_Registry :: get()->env->idartlang;
		$instance = new self($index, $name);

		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'serialized';
		$instance->params['files'] = Aitsu_Core_File :: getImages($idartlang, $pattern);

		return Aitsu_Core_File :: getByMediaId($instance->currentValue());
	}
}