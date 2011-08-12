<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Util_Array {

	public static function shiftAndMix($array, $columns, $length = null) {
		
		if (empty($array)) {
			return $array;
		}
		
		$returnValue = array ();
		$tmp = array ();
		
		$numberOfItems = count($array);
		if ($length != null && $length > $numberOfItems) {
			$numberOfItems = $length;
		}

		$rows = ceil(count($array) / $columns);
		$unused = $rows * $columns -count($array);

		$tmp = array ();
		$col = 0;
		$counter = 0;
		foreach ($array as $key => $value) {
			if ($length != null && $counter >= $length - 1) {
				break;
			}
			$maxEntries = ($columns - $unused > $col) ? $rows : $rows -1;
			if (count($tmp[$col]) == $maxEntries) {
				$col++;
			}
			$tmp[$col][] = array (
				$key,
				$value
			);
			$counter++;
		}

		for ($row = 0; $row < $rows; $row++) {
			for ($col = 0; $col < $columns; $col++) {
				if (isset ($tmp[$col][$row])) {
					$returnValue[$tmp[$col][$row][0]] = $tmp[$col][$row][1];
				}
			}
		}

		return $returnValue;
	}
}