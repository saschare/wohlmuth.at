<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Aitsu_Module_MicroApp_Abstract extends Aitsu_Module_Abstract {

	protected $_user = null;
	protected $_allowEdit = false;
	protected $_cacheIfLoggedIn = true;
	protected $_disableCacheArticleRelation = true;

	public static function init($context) {

		$instance = self :: _getInstance($context['className']);

		$instance->_user = Aitsu_Adm_User :: getInstance();

		if ($instance->_user == null) {
			return '';
		}

		if (!$instance->_user->isAllowed(array (
				'area' => $instance->_moduleName
			))) {
			return '';
		};

		return parent :: init($context, $instance);
	}

	protected function _isAllowed($action) {

		if (is_null($this->_user)) {
			return false;
		}

		return $this->_user->isAllowed(array (
			'area' => $this->_moduleName,
			'action' => $action
		));
	}
}
?>