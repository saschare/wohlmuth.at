<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Syndication_IProxy_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		$view = $this->_getView();

		$page = Aitsu_Content_Config_Link :: set($this->_index, 'IProxy.Source', 'Article', 'Source');
		$view->module = Aitsu_Content_Config_Text :: set($this->_index, 'IProxy.Module', 'Module', 'Source');

		if (!preg_match('/(\\d+)/', $page, $match) || empty($view->module)) {
			return '';
		}

		$view->idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang from _art_lang where idart = :idart and idlang = :idlang', array (
			':idart' => $match[1],
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));

		if (!$view->idartlang) {
			return '';
		}

		return $view->render('index.phtml');
	}
}