<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_ResourceBundle_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('ResourceBundle', true);

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

		$env = Aitsu_Registry :: get()->config->sys->mainDir;
		if (isset (Aitsu_Registry :: get()->config->env) && Aitsu_Registry :: get()->config->env == 'admin') {
			$env = '/admin';
		}

		if ($type == 'js') {
			$output = '<script type="text/javascript" src="' . $env . 'js/' . $uri . '"></script>';
		}

		if ($type == 'css') {
			if (isset ($params->media)) {
				$output = '<link type="text/css" rel="stylesheet" href="' . $env . 'css/' . $uri . '" media="' . $params->media . '" /> ';
			} else {
				$output = '<link type="text/css" rel="stylesheet" href="' . $env . 'css/' . $uri . '" /> ';
			}
		}

		$instance->_save($output, 'eternal');

		return $output;
	}
}