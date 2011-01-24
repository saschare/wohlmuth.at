<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RedirectorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cc155c9-18c0-42eb-97b9-0ab77f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'redirector',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Redirect'),
			'enabled' => self :: getPosition($idart, 'redirector'),
			'position' => self :: getPosition($idart, 'redirector'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('pageproperties', APPLICATION_PATH . '/plugins/article/redirector/forms/redirector.ini');
		$form->title = Aitsu_Translate :: translate('Redirection');
		$form->url = $this->view->url(array (
			'plugin' => 'redirector',
			'paction' => 'index'
		), 'aplugin');

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}
	}
}