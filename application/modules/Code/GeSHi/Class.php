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

		$id = md5($lang . $code);

		$output = '';
		if ($this->_get('Geshi_' . $id, $output)) {
			return $output;
		}

		$output = Aitsu_GeSHi :: parse($code, $lang);

		return $output;
	}
}