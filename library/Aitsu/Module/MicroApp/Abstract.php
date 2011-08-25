<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_MicroApp_Abstract extends Aitsu_Module_Abstract {

	protected $_allowEdit = false;
	protected $_cacheIfLoggedIn = true;
	protected $_disableCacheArticleRelation = true;

	public static function init($context) {

		$user = Aitsu_Adm_User :: getInstance();

		if ($user == null) {
			return '';
		}

		return parent :: init($context);
	}

}
?>