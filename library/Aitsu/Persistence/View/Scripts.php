<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_View_Scripts {

	public static function getAll() {

		$return = array ();

		$scripts = self :: _scanDir(APPLICATION_PATH . '/adm/scripts', '*.php');

		$length = strlen(APPLICATION_PATH . '/adm/scripts');
		$id = 0;
		foreach ($scripts as $script) {
			$id++;
			
			$item = array (
				'id' => $id,
				'path' => $script
			);
			$script = explode('/', substr($script, $length +1));
			$item['className'] = 'Adm_Script_' . substr($script[count($script) - 1], 0, -4);

			include_once ($item['path']);
			$name = call_user_func(array (
				$item['className'],
				'getName'
			));
			$item['name'] = $name;

			if (count($script) == 2) {
				$return[$script[0]][] = (object) $item;
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