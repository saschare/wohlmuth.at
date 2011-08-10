<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Code_GeSHi_Class extends Aitsu_Module_Tree_Abstract {

	protected $_allowEdit = false;

	protected function _init() {

		$lang = $this->_index;
		$code = $this->_context['params'];

		$this->_idSuffix = md5($lang . $code);
	}

	protected function _main() {

		$lang = $this->_index;
		$code = $this->_context['params'];

		return Aitsu_GeSHi :: parse($code, $lang);
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}