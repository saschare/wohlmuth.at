<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Wdrei_Database_Import_Delimited_Abstract {

	protected $_file;
	protected $_table;
	protected $_fields;
	protected $_records;

	/**
	 * Constructor.
	 * @param String Path to the file containing the data.
	 * @param String Name of the table, the data should be stored in.
	 * @param Array Associative array with the field name as the key.
	 */
	public function __construct($file, $table, $fields) {

		$this->_file = $file;
		$this->_table = $table;
		$this->_fields = $fields;

		$this->_mapRecords();
	}

	/**
	 * Reads the records from the file and fills a map
	 * with the result.
	 */
	abstract protected function _mapRecords();

	/**
	 * Saves the results to the specified table of the database.
	 */
	protected function _save() {

		// does nothing so far.
	}
}