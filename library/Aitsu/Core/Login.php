<?php


/**
 * Login.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Login.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Core_Login implements Aitsu_Core_Init_Interface {

	public static function init() {

		$db = Aitsu_Registry :: get()->db;
		$session = Aitsu_Registry :: get()->session;
		
		if (isset ($_REQUEST['logout'])) {
			$session->userId = null;
			return;
		}

		if (!isset ($_POST['loginusername']) || !isset ($_POST['loginpassword'])) {
			return;
		}

		$un = $_POST['loginusername'];
		$pw = md5($_POST['loginpassword']);

		$userId = Aitsu_Db :: fetchOne("" .
		"select idfrontenduser from _frontendusers " .
		"where " .
		"	username = ? " .
		"	and password = ? " .
		"	and active = 1 " .
		"	and (valid_from is null or valid_from < now()) " .
		"	and (valid_to is null or valid_to > now()) " .
		"", array (
			$un,
			$pw
		));

		if (!$userId) {
			return;
		}

		$session->userId = $userId;
	}
}