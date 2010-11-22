<?php


/**
 * Language.
 * @version $Id: Language.php 16535 2010-05-21 08:59:30Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Core_Language {

	public static function getActiveLanguages($idClient) {

		$db = Aitsu_Registry :: get()->db;

		return Aitsu_Db :: fetchAll("" .
		"select " .
		"	lang.idlang, " .
		"	lang.name " .
		"from _lang as lang " .
		"where lang.idclient = ? " .
		"order by lang.name asc ", array (
			$idClient
		));
	}
}