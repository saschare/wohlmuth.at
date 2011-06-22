<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Ammari_Aggregation_Rule_Weekdays implements Ammari_Aggregation_Rule_Interface {

	public static function getDateTimes($start, $end, $params) {

		$start = strtotime($start);
		$end = strtotime($end);
		$weekdays = explode(',', strtolower(str_replace(' ', '', $params)));

		$wd = array (
			'monday' => 1,
			'tuesday' => 2,
			'wednesday' => 3,
			'thursday' => 4,
			'friday' => 5,
			'saturday' => 6,
			'sunday' => 7,
			'montag' => 1
		);
		
		$wds = array();
		foreach ($weekdays as $weekday) {
			$wds[] = $wd[$weekday];
		}

		$return = array();
		
		for ($i = $start; $i <= $end; $i = $i +24 * 60 * 60) {
			if (in_array(date('N', $i), $wds)) {
				$return[] = $i;
			}
		}
		
		return $return;
	}
}