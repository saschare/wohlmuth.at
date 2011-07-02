<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Syndication_RSS_Reader_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$template = isset ($this->_params->template) ? $this->_params->template : 'index';
		$cache = isset ($this->_params->cache) ? $this->_params->cache : (60 * 60);

		$id = md5($this->_params->uri . ' ' . $template);

		$output = '';
		if ($this->_get('Rss_', $output, true)) {
			return $output;
		}

		$view = $this->_getView();

		try {
			$view->channel = new Zend_Feed_Rss($this->_params->uri);
		} catch (Exception $e) {
			$this->_save('', $cache);
			return '';
		}

		$output = $view->render($template . '.phtml');
		$this->_save($output, $cache);

		return $output;
	}
}