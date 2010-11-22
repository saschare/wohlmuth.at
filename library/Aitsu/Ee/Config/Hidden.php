<?php


/**
 * Hidden 'field' configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Hidden.php 17293 2010-06-24 18:10:57Z akm $}
 */

class Aitsu_Ee_Config_Hidden extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {
		return '';
	}

	public static function set($index, $name, $value, $type = 'serialized') {

		if (Aitsu_Registry :: isEdit() && $value !== null) {
			Aitsu_Core_Article_Property :: factory()->setValue('ModuleConfig_' . $index, $name, $value, $type);
		}

		if (empty ($value)) {
			return Aitsu_Core_Article_Property :: factory()->getValue('ModuleConfig_' . $index, $name)->value;
		}

		return $value;
	}
}
?>