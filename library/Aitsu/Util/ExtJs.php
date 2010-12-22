<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_ExtJs {

	public static function encodeFilters($filters) {

		if (empty ($filters)) {
			return null;
		}

		$return = array ();

		foreach ($filters as $filter) {
			$filter = (object) $filter;

			$tFilter = $filter->field . ' ';

			if ($filter->data['type'] == 'string') {
				$tFilter .= 'like';
			} else {
				$tFilter .= self :: _translateComparison($filter->data['comparison']);
			}

			if ($filter->data['type'] == 'date' && $filter->data['comparison'] == 'eq') {
				$return[] = (object) array (
					'clause' => "date_format({$filter->field}, '%Y-%m-%d')" . ' =',
					'value' => $filter->data['value']
				);
			} else {
				$return[] = (object) array (
					'clause' => $tFilter,
					'value' => str_replace('*', '%', $filter->data['value'])
				);
			}
		}

		return $return;
	}

	protected static function _translateComparison($comparison) {

		if ($comparison == 'gt') {
			return '>';
		}

		if ($comparison == 'lt') {
			return '<';
		}

		if ($comparison == 'eq') {
			return '=';
		}
		
		return '?';
	}
}