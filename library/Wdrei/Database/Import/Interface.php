<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

interface Wdrei_Database_Import_Interface {
	
	public static function import($file, $table, $fields);
}