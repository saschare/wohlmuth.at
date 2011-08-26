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

	/*
	 * If set to true, the CUG.Login module is printed out if the user
	 * is not logged in. Set to false to give blank output instead.
	 */
	protected $_autoLogin = true;

	public static function init($context) {

		$instance = self :: _getInstance($context['className']);

		$instance->_user = Aitsu_Adm_User :: getInstance();

		if ($instance->_user == null) {
			if ($instance->_autoLogin) {
				return '<script type="application/x-aitsu" src="CUG.Login"></script>';
			}
			return '';
		}

		/*
		 * The access check is only done in the frontend as in the backend
		 * the functionality is suppressed anyway.
		 */
		if (!Aitsu_Application_Status :: isEdit() && !$instance->_user->isAllowed(array (
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

	protected function _transformOutput($output) {

		if (!Aitsu_Application_Status :: isEdit()) {
			return $output;
		}

		return '' .
		'<div style="border:1px solid black; padding:5px;">' .
		'	The MicroApp <strong>' . $this->_moduleName . '</strong> is ' .
		'	available in the frontend only.' .
		'</div>';
	}

}