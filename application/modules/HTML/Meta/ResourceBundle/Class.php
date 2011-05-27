<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

class Module_HTML_Meta_ResourceBundle_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.ResourceBundle', true);

		$instance = new self();

		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		$type = $params->type;

		$output = '';
		if ($instance->_get('ResourceBundle_' . $type, $output)) {
			return $output;
		}

		$resources = array ();
		foreach ($params->res as $key => $resource) {
			$resources[] = $resource;
		}

		$uri = Aitsu_Ee_MiniMe :: getUri($type, $resources);

		$env = '';
		if (isset (Aitsu_Registry :: get()->config->env) && Aitsu_Registry :: get()->config->env == 'admin') {
			$env = '/admin';
		}

		if ($type == 'js') {
			$output = '<script type="text/javascript" src="' . $env . '/js/' . $uri . '"></script>';
		}

		if ($type == 'css') {
			if (isset ($params->media)) {
				$output = '<link type="text/css" rel="stylesheet" href="' . $env . '/css/' . $uri . '" media="' . $params->media . '" /> ';
			} else {
				$output = '<link type="text/css" rel="stylesheet" href="' . $env . '/css/' . $uri . '" /> ';
			}
		}

		$instance->_save($output, 'eternal');

		return $output;
	}
}