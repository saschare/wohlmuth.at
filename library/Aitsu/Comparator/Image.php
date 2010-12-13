<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Comparator_Image {

	protected static $_asc = true;

	public static function asc($set = true) {

		self :: $_asc = $set;
	}

	public static function desc($set = false) {

		self :: $_asc = !$set;
	}

	public static function filename($a, $b) {

		if (self :: $_asc) {
			return ($a->filename > $b->filename) ? 1 : -1;
		}

		return ($a->filename < $b->filename) ? 1 : -1;
	}

	public static function sort(& $source, $method, $asc = true) {

		self :: asc($asc);

		uasort($source, array (
			'self',
			$method
		));
	}
}