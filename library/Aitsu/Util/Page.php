<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */

class Aitsu_Util_Page {

	/**
	 * The method takes one parameter, usually an idart or - for convenience -
	 * an URL, too. If the parameter is not an integer it is assumed to be
	 * an URL and is returned as is. Otherwise the methode resolves the idart
	 * to an URL using the current language seetings. If the idart cannot be
	 * resolved, the method returns NULL.
	 * 
	 * @var Mixed Idart or Url.
	 * @return String Net URL of the page.
	 */
	public static function getUrlByIdart($idart) {

		if (Aitsu_Util_Type :: integer($idart)) {
			/*
			 * The parameter provided is not an integer. We assume it to be
			 * an URL and we return it unchanged.
			 */
			return $idart;
		}

		$url = Aitsu_Db :: fetchOne('' .
		'', array (
			':idart' => $idart,
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));
	}

}