<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Adm_Script_Removecaturls extends Aitsu_Adm_Script_Abstract {

	protected $_medium = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Remove category URLs');
	}

	public function doDrop() {

		Aitsu_Db :: query('' .
		'update _cat_lang set url = null where idlang = :idlang', array (
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('URLs removed.'));
	}

}