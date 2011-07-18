<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Syndication_RSS_Reader_Class extends Aitsu_Module_Tree_Abstract {

	protected function _init() {

		$template = isset ($this->_params->template) ? $this->_params->template : 'index';
		$cache = isset ($this->_params->cache) ? $this->_params->cache : (60 * 60);

		$id = md5($this->_params->uri . ' ' . $template);

		$view = $this->_getView();
		
		$uri = isset($this->_params->uri) ? $this->_params->uri : Aitsu_Content_Config_Text :: set($this->_index, 'RSS.Reader.URI', 'URI', 'RSS');

		if (empty($uri)) {
			return '';
		}

		try {
			$view->channel = new Zend_Feed_Rss($uri);
		} catch (Exception $e) {
			return '';
		}

		$output = $view->render($template . '.phtml');

		return $output;
	}

	protected function _cachingPeriod() {

		if (isset ($this->_params->cache)) {
			return $this->_params->cache;
		}

		return 60 * 60;
	}
}