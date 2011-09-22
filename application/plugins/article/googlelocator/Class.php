<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class GooglelocatorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e7b5a8c-7ac8-4f3e-8a7f-12057f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'googlelocator',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Google Locator'),
			'enabled' => self :: getPosition($idart, 'googlelocator'),
			'position' => self :: getPosition($idart, 'googlelocator'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

	}

}