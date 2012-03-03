<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Util_Array {

	/**
	 * The method returns a reordered clone of the given array in a way, that, if you
	 * output its content one item after the other, line for line the result is ordered
	 * as columns instead of lines.
	 * A B C D E F G H I J K L M N O P Q R S T
	 * ... is sorted to (using 4 as the columns parameter)
	 * A E I M O B F J N R C G K O S D H L P T
	 * If you output the result in left floating boxes you will get...
	 * A E I M O
	 * B F J N R
	 * C G K O S
	 * D H L P T
	 * @var Array Array to be remixed.
	 * @var Integer Number of columns to be used.
	 * @var Integer Maximum length of the resulting array.
	 * @return Array The reordered array.
	 */
	public static function shiftAndMix($array, $columns, $length = null) {
		
		if (empty($array)) {
			return $array;
		}
		
		$returnValue = array ();
		$tmp = array ();
		
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