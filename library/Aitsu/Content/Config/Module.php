<?php


/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */

class Aitsu_Content_Config_Module extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'module.phtml';
	}

	public static function set($index, $name, $label, $keyValuePairs, $fieldset) {

		$instance = new self($index, $name);

                $instance->facts['tab'] = true;
                $instance->facts['label'] = $label;
		$instance->params['keyValuePairs'] = $keyValuePairs;
                $instance->facts['type'] = 'serialized';

		return $instance->currentValue();
	}
}