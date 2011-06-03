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
class Aitsu_Ee_Config_ArticleOrCategory extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'Category.phtml';
	}

	public static function set($index, $name, $label, $fieldset = '') {

		$instance = new self($index, $name);

		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'text';

		return $instance->currentValue();
	}
}