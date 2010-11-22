<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19142 2010-10-04 09:49:15Z akm $}
 */

/*
 * This plugin is in development and not ready to 
 * be used in a production environment.
 */

class SeminarArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cdaac42-b0a4-4983-a667-33c97f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'seminar',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Seminars'),
			'enabled' => self :: getPosition($idart, 'enabled'),
			'position' => self :: getPosition($idart, 'position'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

	}
}