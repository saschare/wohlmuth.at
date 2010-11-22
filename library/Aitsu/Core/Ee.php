<?php


/**
 * Checks whether or not the current installation is
 * an enterprise edition.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Ee.php 17855 2010-07-30 17:27:13Z akm $}
 */

class Aitsu_Core_Ee {
	
	public static function available() {
		
		return file_exists(realpath(dirname(__FILE__) . '/../Ee'));
	}
}