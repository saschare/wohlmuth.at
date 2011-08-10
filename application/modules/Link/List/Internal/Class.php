<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Link_List_Internal_Class extends Aitsu_Module_Tree_Abstract {

	protected $_isVolatile = true;
	protected $_allowEdit = false;

	protected function _main() {

		$view = $this->_getView();

		$view->links = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.* ' .
		'from _crosslink crosslink ' .
		'left join _art_lang artlang on artlang.idartlang in (crosslink.idartlanglow, crosslink.idartlanghigh) ' .
		'where ' .
		'	:idartlang in (crosslink.idartlanglow, crosslink.idartlanghigh) ' .
		'	and artlang.idartlang != :idartlang ' .
		'order by ' .
		'	artlang.pagetitle asc, ' .
		'	artlang.created asc ', array (
			':idartlang' => Aitsu_Registry :: get()->env->idartlang
		));
		
		if (empty($view->links)) {
			return '';
		}

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}