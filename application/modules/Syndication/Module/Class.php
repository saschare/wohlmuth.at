<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Syndication_Module_Class extends Aitsu_Module_Tree_Abstract {

	protected $_isVolatile = false;

	public function _main() {

		$view = $this->_getView();
		
		$page = Aitsu_Content_Config_Link :: set($this->_index, 'Syndication.Module.' . $this->_params->genuineType, 'Article', 'Source');
		
		if (!preg_match('/(\\d+)/', $page, $match)) {
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

		$view->module = $this->_params->genuineType;
		$view->index = $this->_index;

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}