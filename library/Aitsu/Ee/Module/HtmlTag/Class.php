<?php


/**
 * HTML tag ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 16855 2010-06-04 14:57:16Z akm $}
 */

class Aitsu_Ee_Module_HtmlTag_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'HTML tag',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns the specified HTML tag with the given content.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536c-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$return = '';

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$attributes = array ();

		$attr = array ();
		$edit = false;
		foreach ($params->attr as $key => $value) {
			if (is_object($value)) {
				$edit = true;
				$value = self :: getValue($index, $key, $value->type, $value->default, $params->tag, $params);
					$attributes[] = $key . '="' . $value . '"';

			} else {
				$attributes[] = $key . '="' . $value . '"';
			}
			$attr[$key] = $value;
		}

		if (!$edit) {
			Aitsu_Content_Edit :: noEdit('HtmlTag', true);
		}

		$return .= '<' . $params->tag . ' ' . implode(' ', $attributes);

		if (isset ($params->content)) {
			$return .= '>' . $params->content;
		} else {
			$return .= ' />';
		}

		if (Aitsu_Registry :: isEdit()) {
			$return = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $return;
		}

		foreach ($attr as $key => $value) {
			$return = str_replace('{' . $key . '}', $value, $return);
		}

		return $return;
	}

	protected static function getValue($index, $key, $type, $default, $tag, $params) {

		$value = null;

		if ($type == 'text') {
			$value = Aitsu_Ee_Config_Text :: set($index, $key, $key, $tag);

		}

		if ($type == 'image') {
			$value = Aitsu_Ee_Config_Images :: set($index, $key, '', $key);
			if (!empty ($value)) {
				$value = 'image/{width}/{height}/1/' . Aitsu_Registry :: get()->env->idart . '/' . $value[array_rand($value)]->filename;
			}
		}

		if ($type == 'file') {
			$value = Aitsu_Ee_Config_Files :: set($index, $key, '', $key);
			$value = 'file/' . Aitsu_Registry :: get()->env->idart . '/' . $value[array_rand($value)]->filename;
		}

		if ($type == 'date') {
			$value = Aitsu_Ee_Config_Date :: set($index, $key, $key, $tag);
		}

		if (empty ($value)) {
			return $default;
		}

		return $value;
	}
}