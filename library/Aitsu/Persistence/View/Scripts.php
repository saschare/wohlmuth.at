<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Scripts.php 18675 2010-09-09 18:21:07Z akm $}
 */

class Aitsu_Persistence_View_Scripts {

	public static function getAll() {

		$return = array ();

		$scripts = self :: _scanDir(APPLICATION_PATH . '/adm/scripts', '*.php');

		$length = strlen(APPLICATION_PATH . '/adm/scripts');
		foreach ($scripts as $script) {
			$item = array (
				'path' => $script
			);
			$script = explode('/', substr($script, $length +1));
			$item['class'] = 'Adm_Script_' . substr($script[count($script) - 1], 0, -4);

			include_once ($item['path']);
			$name = call_user_func(array (
				$item['class'],
				'getName'
			));

			if (count($script) == 2) {
				$return[$script[0]][$name] = (object) $item;
			}
		}

		return $return;
	}

	protected static function _scanDir($dir, $pattern = '*.*') {

		$return = array ();

		$files = scandir($dir);

		foreach ($files as $file) {
			if ($file != '.' && $file != '..') {
				if (is_dir($dir . '/' . $file)) {
					$return = array_merge($return, self :: _scanDir($dir . '/' . $file, $pattern));
				}
				elseif (fnmatch($pattern, $file)) {
					$return[] = $dir . '/' . $file;
				}
			}
		}

		return $return;
	}
}