<?php


/**
 * Adds a resource bundle (CSS or JS).
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 18339 2010-08-25 17:45:12Z akm $}
 */

class Aitsu_Ee_Module_ResourceBundle_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Resource bundle',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Bundles the specified ressources (CSS or JS) to a single file and outputs a single reference containing the content of the specified resources.'),
			'type' => array (
				'Header',
				'Layout'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725372-c565-11df-851a-0800200c9a66'
		);
	}

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