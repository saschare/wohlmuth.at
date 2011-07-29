<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_Tree_Abstract extends Aitsu_Module_Abstract {

	public static function init($context) {

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

			$index = preg_replace('/[^a-zA-Z_0-9]/', '_', $context['index']);
			$index = str_replace('.', '_', $index);
		}

		$type = null;
		if (!empty ($types)) {
			$type = Aitsu_Content_Config_Select :: set($index, $genuineType . '.SubType', 'Subtype', $types, 'Type');
		}

		if (!empty ($type) && $type != $moduleName && array_key_exists($type, $types)) {
			/*
			 * A subtype has to be used instead of the genuine one.
			 */
			return '' .
			'<script type="application/x-aitsu" src="' . $type . ':' . $context['index'] . '">' . "\n" .
			'	contextType = ' . $context['className'] . '' . "\n" .
			'	genuineType = ' . $genuineType . '' . "\n" . $context['params'] . "\n" .
			'</script>';
		}

		$output = parent :: init($context);

		if (Aitsu_Application_Status :: isEdit()) {
			$maxLength = 60;
			$index = strlen($context['index']) > $maxLength ? substr($context['index'], 0, $maxLength) . '...' : $context['index'];

			if (trim($output) == '' && $this->_allowEdit) {
				preg_match('/^Module_(.*?)_Class$/', $context['className'], $match);
				$moduleName = str_replace('_', '.', $match[1]);
				return '' .
				'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
				'<div style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' .
				'	<div style="height:15px; background-color: #CCC; color: white; font-size: 11px; padding:2px 5px 0 5px;">' .
				'		<span style="font-weight:bold; float:left;">' . $index . '</span><span style="float:right;">Module <span style="font-weight:bold;">' . $moduleName . '</span></span>' .
				'	</div>' .
				'	<div>' .
				'		' . $output . '' .
				'	</div>' .
				'</div>';
			}

			return '' .
			'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
			'<div>' . $output . '</div>';
		}

		return $output;
	}

}