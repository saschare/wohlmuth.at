<?php


/**
 * aitsu plugin controller.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Controller.php 16946 2010-06-09 21:51:45Z akm $}
 */

class Aitsu_Core_Plugin_Controller {

	protected $plugins;

	protected static function _getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	protected function __construct() {

		$this->plugins['article'] = array ();
		$plugins = scandir(APPLICATION_PATH . '/plugins/article');

		foreach ($plugins as $plugin) {
			$pathInfo = pathinfo($plugin);
			if (isset ($pathInfo['extension']) && $pathInfo['extension'] == 'php') {
				include (APPLICATION_PATH . '/plugins/article/' . $plugin);
				$className = self :: _getClassName($plugin);
				if (in_array('Aitsu_Core_Plugin_Article_Interface', class_implements($className))) {
					$o = call_user_func(array (
						$className,
						'_getInstance'
					));
					$details = $o->register();
					if ($details->enabled) {
						$this->plugins['article'][$className] = $details;
						$o->allow($className, uniqid(), 60 * 15);
					}
				}
			}
		}

		uasort($this->plugins['article'], array (
			$this,
			'_comparePosition'
		));
	}

	protected function _comparePosition($a, $b) {

		if (isset ($a->position) && isset ($b->position)) {
			return $a->position < $b->position ? -1 : 1;
		}

		if (isset ($a->position)) {
			return -1;
		}

		if (isset ($b->position)) {
			return 1;
		}

		return strcmp($a->name, $b->name);
	}

	public static function getArticlePlugins() {

		return self :: _getInstance()->plugins['article'];
	}

	protected static function _getClassName($filename) {
		return basename($filename, '.php');
	}

	public static function delegate($plugin, $action = 'index', $id = false, $args = array ()) {

		include_once (APPLICATION_PATH . '/plugins/article/' . $plugin . '.php');

		$action = $action . 'Action';

		try {
			$o = call_user_func(array (
				self :: _getClassName($plugin),
				'_getInstance'
			));
			if ($id !== false && !call_user_func_array(array (
					$o,
					'isAllowed'
				), array (
					$plugin,
					$id
				))) {
				throw new Exception('User is not allowed to fire the action');
			}
			if ($id !== false) {
				call_user_func_array(array (
					$o,
					'setId'
				), array (
					$id
				));
			}
			return call_user_func_array(array (
				$o,
				$action
			), $args);
		} catch (Exception $e) {
			header('HTTP/1.1 401 Unauthorized');
			Aitsu_Core_Logger :: log($e->getMessage(), Zend_Log :: INFO);
			exit ();
		}
	}
}