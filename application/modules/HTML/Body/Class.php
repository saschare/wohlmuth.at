<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Body_Class extends Aitsu_Module_Tree_Abstract {

	protected function _main() {

		$view = $this->_getView();

		$view->schemaOrgWebPageType = Aitsu_Db :: fetchOne('' .
		'select ' .
		'	schemaorg.type ' .
		'from _art_meta artmeta ' .
		'left join _schemaorgtype schemaorg on schemaorg.schemaorgtypeid = artmeta.schemaorgtype ' .
		'where ' .
		'	artmeta.idartlang = :idartlang', array (
			':idartlang' => Aitsu_Registry :: get()->env->idartlang
		));

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}