<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_Rss_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();

		$index = $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$template = isset ($params->template) ? $params->template : 'index';
		$cache = isset ($params->cache) ? $params->cache : (60 * 60);

		$id = md5($params->uri . ' ' . $template);

		$output = '';
		if ($instance->_get('Rss_' . $id, $output, true)) {
			return $output;
		}

		$view = $instance->_getView();

		try {
			$view->channel = new Zend_Feed_Rss($params->uri);
		} catch (Exception $e) {
			$instance->_save('', $cache);
			return '';
		}

		$output = $view->render($template . '.phtml');
		$instance->_save($output, $cache);

		return $output;
	}
}