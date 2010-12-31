<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_Dir {
	
	public static function scan($path, $pattern = '*') {
		
		$return = array();
		
		self :: _scanDir($path, $return, $pattern);
		
		return $return;
	}

	protected static function _scanDir($path, & $files, $pattern) {

		if (!is_dir($path) || !is_readable($path)) {
			return;
		}

		$content = scandir($path);

		foreach ($content as $file) {
			if ($file != '.' && $file != '..') {
				if (is_dir($path . '/' . $file)) {
					self :: _scanDir($path . '/' . $file, $files, $pattern);
				} elseif (fnmatch($pattern, $file)) {
					$files[] = $path . '/' . $file;
				}
			}
		}
	}

}