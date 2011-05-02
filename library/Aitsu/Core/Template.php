<?php


/**
 * aitsu template.
 * @version $Id: Template.php 16535 2010-05-21 08:59:30Z akm $
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Core_Template {

	public static function getTemplates($idlang) {

		$db = Aitsu_Registry :: get()->db;

		return Aitsu_Db :: fetchAll("" .
		"select " .
		"	template.idtpl, " .
		"	template.name  " .
		"from _template as template " .
		"left join _lang as clientlang on template.idclient = clientlang.idclient " .
		"where " .
		"	clientlang.idlang = ? " .
		"order by " .
		"	template.name asc ", array (
			$idlang
		));
	}
}