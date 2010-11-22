<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19916 2010-11-17 12:40:58Z akm $}
 */

/*
 * This plugin is in development and not ready to be
 * used in a production environment.
 */

class DatesArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cc86562-4994-4dcf-9968-18e17f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'dates',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Dates'),
			'enabled' => self :: getPosition($idart, 'dates'),
			'position' => self :: getPosition($idart, 'dates'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idart = $this->getRequest()->getParam('idart');
	}

}