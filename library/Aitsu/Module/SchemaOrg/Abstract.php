<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_SchemaOrg_Abstract extends Aitsu_Module_Abstract {

	public static function init($context) {

		preg_match('/Module_Schema_Org_(.*?)_Class$/', $context['className'], $match);
		
		$schemaHierachy = new Zend_Config_Ini(dirname(__FILE__) . '/hierarchy.ini');
		$schemaTree = $schemaHierachy->toArray();

		if (!empty ($context['params'])) {
			$params = Aitsu_Util :: parseSimpleIni($context['params']);
		}

		if (isset ($params->genuineType)) {
			$genuineType = $params->genuineType;
		} else {
			$genuineType = $match[1];
		}

		$types = array ();
		self :: _getChildrenOf($genuineType, $types, $schemaTree);
		ksort($types);

		$index = preg_replace('/[^a-zA-Z_0-9]/', '_', $context['index']);
		$index = str_replace('.', '_', $index);

		$type = Aitsu_Content_Config_Select :: set($index, 'schema.org.Type', 'Subtype', $types, 'Type');

		if (!empty ($type) && $type != $match[1]) {
			/*
			 * A subtype has to be used instead of the genuine one.
			 */
			return '' .
			'<script type="application/x-aitsu" src="Schema.Org.' . $type . ':' . $context['index'] . '">' . "\n" .
			'	genuineType = ' . $genuineType . '' . "\n" . $context['params'] . "\n" .
			'</script>';
		}

		$output = parent :: init($context);

		if (Aitsu_Application_Status :: isEdit()) {
			$maxLength = 60;
			$index = strlen($context['index']) > $maxLength ? substr($context['index'], 0, $maxLength) . '...' : $context['index'];

			return '' .
			'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
			'<div style="border:1px dashed #CCC; padding:2px 2px 0 2px;">' .
			'	<div style="height:15px; background-color: #CCC; color: white; font-size: 11px; padding:2px 5px 0 5px;">' .
			'		<span style="font-weight:bold; float:left;">' . $index . '</span><span style="float:right;">schema.org <span style="font-weight:bold;">' . $match[1] . '</span></span>' .
			'	</div>' .
			'	<div style="padding:5px 3px 5px 3px;">' .
			'		' . $output . '' .
			'	</div>' .
			'</div>';
		}

		return $output;
	}

	protected function _getView() {

		$view = parent :: _getView(new Aitsu_Module_SchemaOrg_View());

		preg_match('/Module_Schema_Org_(.*?)_Class/', get_class($this), $match);
		$view->SchemaOrgType = $match[1];

		$view->idart = Aitsu_Registry :: get()->env->idart;
		$view->description = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.Thing.Description', 'Description', 'Thing');

		$images = Aitsu_Db :: fetchCol('' .
		'select distinct image.filename ' .
		'from _media image ' .
		'where ' .
		'	image.deleted is null ' .
		'	and image.idart = :idart ' .
		'order by ' .
		'	image.filename', array (
			':idart' => Aitsu_Registry :: get()->env->idart
		));

		if (!$images) {
			$view->image = null;
		} else {
			$keyValuePairs = array (
				'No image' => ''
			);
			foreach ($images as $image) {
				$keyValuePairs[$image] = $image;
			}
			$view->image = Aitsu_Content_Config_Select :: set($this->_index, 'schema.org.Thing.Image', 'Image', $keyValuePairs, 'Thing');
		}

		if (!in_array($view->image, $images)) {
			$view->image = null;
		}

		$view->name = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.Name', 'Name', 'Thing');
		$view->url = Aitsu_Content_Config_Text :: set($this->_index, 'schema.org.Thing.URL', 'URL', 'Thing');

		return $view;
	}

	protected static function _getChildrenOf($type, & $result, & $subSet, $in = false) {

		foreach ($subSet as $key => $value) {
			if ($in || $type == $key) {
				$result[$key] = $key;
			}
			if (is_array($value)) {
				self :: _getChildrenOf($type, $result, $value, $in || $type == $key);
			}
		}
	}
}