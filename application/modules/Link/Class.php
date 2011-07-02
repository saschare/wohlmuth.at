<?php


/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011,webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Link_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: isBlock('Link', false);

		$name = Aitsu_Content_Config_Text :: set($this->_index, 'name', 'Name', 'Link');
		$link = Aitsu_Content_Config_Link :: set($this->_index, 'link', 'Link', 'Link');

		$targets = array (
			'_blank' => '_blank',
			'_top' => '_top',
			'_self' => '_self',
			'_parent' => '_parent'
		);

		$target = Aitsu_Content_Config_Select :: set($this->_index, 'target', 'Target', $targets, 'Link');

		if (strpos($link, 'idcat') !== false || strpos($link, 'idart') !== false) {
			$link = str_replace(' ', '-', $link);
			$link = '{ref:' . $link . '}';
		}

		if (empty ($link) && Aitsu_Registry :: isEdit()) {
			return '<a href="#">no link given</a>';
		} else {
			if (!empty ($link)) {
				return '<a href="' . $link . '" target="' . $target . '">' . $name . '</a>';
			}
		}
	}

}