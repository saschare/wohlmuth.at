<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Content_Config_Hidden extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {
		return '';
	}

	public static function set($index, $name, $value, $type = 'serialized') {

		if (Aitsu_Registry :: isEdit() && $value !== null) {
			Aitsu_Core_Article_Property :: factory()->setValue('ModuleConfig_' . $index, $name, $value, $type);
		}

		if (empty ($value)) {
			$val = Aitsu_Core_Article_Property :: factory()->getValue('ModuleConfig_' . $index, $name);
			return ($val == null) ? null : $val->value;
		}

		return $value;
	}
}
?>