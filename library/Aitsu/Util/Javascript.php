<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Util_Javascript {

	protected $js = array ();
	protected $reference = array();

	protected function __construct() {
	}

	protected static function _getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function add($js) {

		self :: _getInstance()->js[] = $js;
	}
	
	public static function addReference($js) {
		
		self :: _getInstance()->reference[] = $js;
	}

	public static function get() {

		if (count(self :: _getInstance()->js) == 0) {
			return self :: _getInstance()->js;
		}

		$js = array_unique(self :: _getInstance()->js);

		return implode('', $js);
	}
	
	public static function getReferences() {
		
		if (count(self :: _getInstance()->reference) == 0) {
			return self :: _getInstance()->reference;
		}
		
		$reference = array_unique(self :: _getInstance()->reference);
		
		return $reference;
	}
	
	public static function getArrayString($data, $fields) {
		
		$return = array();
		
		foreach ($data as $entry) {
			$entry = (object) $entry;
			$row = array();
			foreach ($fields as $field) {
				$row[] = "'" . addslashes($entry->$field) . "'";
			}
			$return[] = '[' . implode(',', $row) . ']';
		}
		
		return '[' . implode(',', $return) . ']';
	}
}