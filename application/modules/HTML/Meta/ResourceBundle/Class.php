<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_HTML_Meta_ResourceBundle_Class extends Aitsu_Module_Abstract {

	protected $_allowEdit = false;
	protected $_cacheIfLoggedIn = true;
	protected $_disableCacheArticleRelation = true;

	protected function _main() {

		$type = $this->_params->type;

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
		
		return $output;
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}