<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Execute_Init implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		if (!isset ($_GET['class'])) {
			return;
		}

		$parts = explode('/', $_GET['class']);
		foreach ($parts as & $part) {
			$part = ucfirst($part);
		}
		$className = implode('_', $parts);
		$classPath = realpath(APPLICATION_PATH . '/..') . '/library/' . implode('/', $parts) . '.php';

		if (!is_file($classPath)) {
			return;
		}

		require_once ($classPath);

		if (!in_array('Aitsu_Execute_Interface', class_implements($className))) {
			return;
		}

		echo call_user_func(array (
			$className,
			'execute'
		));

		exit (0);
	}
}