<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_Factory {

	public static function get($policy, $statement, $idartlang = null) {

		$idartlang = is_null($idartlang) ? Aitsu_Registry :: get()->env->idartlang : $idartlang;
		$className = 'Aitsu_Article_Policy_' . $policy;

		return new $className ($statement, $idartlang);
	}
}