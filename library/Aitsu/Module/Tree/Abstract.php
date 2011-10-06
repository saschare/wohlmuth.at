<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_Tree_Abstract extends Aitsu_Module_Abstract {

	public static function init($context, $instance = null) {

		if (!empty ($context['params'])) {
			$params = Aitsu_Util :: parseSimpleIni($context['params']);
		}

		if (isset ($params->genuineType)) {
			$genuineType = $params->genuineType;
		} else {
			$genuineType = $context['className'];
			preg_match('/^(?:Skin_)?Module_(.*?)_Class$/', $genuineType, $match);
			$genuineType = str_replace('_', '.', $match[1]);
		}

		preg_match('/^(?:Skin_)?Module_(.*?)_Class$/', $context['className'], $match);
		$moduleName = str_replace('_', '.', $match[1]);

		$types = array ();

		$index = preg_replace('/[^a-zA-Z_0-9]/', '_', $context['index']);
		$index = str_replace('.', '_', $index);

		if (Aitsu_Application_Status :: isEdit()) {
			if (is_readable(APPLICATION_PATH . '/skins/' . Aitsu_Config :: get('skin') . '/module/hierarchy.ini')) {
				$schemaHierachy = new Zend_Config_Ini(APPLICATION_PATH . '/skins/' . Aitsu_Config :: get('skin') . '/module/hierarchy.ini');
			} else {
				$schemaHierachy = new Zend_Config_Ini(APPLICATION_PATH . '/modules/hierarchy.ini');
			}

			$schemaTree = $schemaHierachy->toArray();

			$types = array ();
			self :: _getChildrenOf($genuineType, $types, $schemaTree);
			ksort($types);
		}

		$type = Aitsu_Content_Config_Select :: set($index, $genuineType . '.SubType', 'Subtype', $types, 'Type');

		if (!empty ($type) && $type != $moduleName) {
			/*
			 * A subtype has to be used instead of the genuine one.
			 */
			return '' .
			'<script type="application/x-aitsu" src="' . $type . ':' . $context['index'] . '">' . "\n" .
			'	contextType = ' . $context['className'] . '' . "\n" .
			'	genuineType = ' . $genuineType . '' . "\n" . $context['params'] . "\n" .
			'</script>';
		}

		return parent :: init($context);
	}

}