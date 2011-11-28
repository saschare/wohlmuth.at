<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Link_Class extends Aitsu_Module_Tree_Abstract {

	protected $_isBlock = false;

	protected function _main() {

		$view = $this->_getView();

		$view->name = Aitsu_Content_Config_Text :: set($this->_index, 'name', 'Name', 'Link');
		$view->link = Aitsu_Content_Config_Link :: set($this->_index, 'link', 'Link', 'Link');

		$targets = array (
			'_blank' => '_blank',
			'_top' => '_top',
			'_self' => '_self',
			'_parent' => '_parent'
		);

		$view->target = Aitsu_Content_Config_Select :: set($this->_index, 'target', 'Target', $targets, 'Link');

		if (strpos($view->link, 'idcat') !== false || strpos($view->link, 'idart') !== false) {
			$view->link = str_replace(' ', '-', $view->link);
			$view->link = '{ref:' . $view->link . '}';
		}

		if (empty ($view->link) || empty ($view->name)) {
			return '';
		}

		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}