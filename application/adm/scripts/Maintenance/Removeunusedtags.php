<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Adm_Script_Removeunusedtags extends Aitsu_Adm_Script_Abstract {

	protected $_medium = null;

	public static function getName() {

		return Aitsu_Translate :: translate('Remove unused tags');
	}

	public function doDrop() {

		Aitsu_Db :: query('' .
		'delete from _tag ' .
		'where ' .
		'	tagid not in (select distinct tagid from _tag_art)');

		return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate :: translate('Unused tags have been removed.'));
	}

}