<?php


/**
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 * 
 * {@id $Id: Class.php 19916 2010-11-17 12:40:58Z akm $}
 */

/*
 * This plugin is in development and not ready to be used
 * in a production environment.
 */

class ArbitrarydataArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'arbitrarydata',
			'tabname' => Aitsu_Translate :: translate('Arbitrary data'),
			'enabled' => self :: getPosition($idart, 'arbitrarydata'),
			'position' => self :: getPosition($idart, 'arbitrarydata'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->test = Aitsu_Persistence_Generic :: factory(408, '_cat_lang')->load()->getData();
	}
}