<?php


/**
 * The module outputs a back link if all of the following
 * conditions are met:
 * 
 * - The environmental variable HTTP_REFERER is set and contains a value.
 * - The envirnomental variable HTTP_HOST is set and contains a value.
 * - The main domain of the referer equals that of the host.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Navigation_Back_Class extends Aitsu_Module_Tree_Abstract {

	protected $_allowEdit = false;

	protected function _init() {

		if (!isset ($_SERVER['HTTP_REFERER'])) {
			/*
			 * Referer is not set. No output is made.
			 */
			return '';
		}

		$host = $_SERVER['HTTP_REFERER'];
		$parts = parse_url($host);
		preg_match('/[^\\.]*\\.[^\\.]*$/', $parts['host'], $match);
		$refHost = $match[0];

		preg_match('/[^\\.]*\\.[^\\.]*$/', $_SERVER['HTTP_HOST'], $match);
		$curHost = $match[0];

		if ($refHost != $curHost) {
			/*
			 * The referer is a foreign domain. No output is made.
			 */
			return '';
		}

		$template = isset ($this->_params->template) ? $this->_params->template : 'index';

		$view = $this->_getView();
		$view->url = $host;
		return $view->render($template . '.phtml');
	}

}