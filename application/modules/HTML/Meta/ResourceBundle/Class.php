<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2011, w3concepts AG
 */

class Module_HTML_Meta_ResourceBundle_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.ResourceBundle', true);

		$type = $this->_params->type;

		$output = '';
		if ($this->_get('ResourceBundle_' . $type, $output)) {
			return $output;
		}

		$resources = array ();
		foreach ($this->_params->res as $key => $resource) {
			$resources[] = $resource;
		}

		$uri = Aitsu_Ee_MiniMe :: getUri($type, $resources);

		$env = Aitsu_Config :: get('env') == null ? '' : Aitsu_Config :: get('env');

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

		$this->_save($output, 'eternal');

		return $output;
	}
}