<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Aitsu_Ee_Module_Rss_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'RSS',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Read the specified RSS source and outputs its content using the specified template.'),
			'type' => array (
				'Content',
				'Foreign sources'
			),
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725373-c565-11df-851a-0800200c9a66'
		);
	}

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