<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Wdrei_Database_Import_Delimited_Tabs extends Wdrei_Database_Import_Delimited_Abstract implements Wdrei_Database_Import_Interface {

	public static function import($file, $table, $fields) {

		new self($file, $table, $fields);
	}

	protected function _mapRecords() {

		// does nothing so far.
	}
}