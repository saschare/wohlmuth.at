<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
 
include_once (APPLICATION_PATH . '/modules/List/Download/Class.php');
 
class Skin_Module_List_Download_Class extends Module_List_Download_Class {

	protected function _cachingPeriod() {

		return Aitsu_Util_Date :: secondsUntilEndOf('day');
	}
}