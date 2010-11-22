<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19142 2010-10-04 09:49:15Z akm $}
 */

class DcArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cab1ded-59b0-40dd-a74c-0bfe7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'dc',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Dublin core (DC)'),
			'enabled' => self :: getPosition($idart, 'dc'),
			'position' => self :: getPosition($idart, 'dc'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/dc/forms/meta.ini', 'edit'));
		$form->setAction($this->view->url());

		$data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();

		if (!$this->getRequest()->isPost()) {
			$form->setValues($data->toArray());
		}

		$this->view->pluginId = self :: ID;
		$this->view->form = $form;

		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($_POST)) {
			$this->_helper->json((object) array (
				'status' => 'validationfailure',
				'message' => $this->view->render('index.phtml')
			));
		}

		try {
			$data->setValues($form->getValues());
			$data->save();
			$form->setValues($data->toArray());
			$this->_helper->json((object) array (
				'status' => 'success',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('DC.meta data saved.'),
				'data' => (object) $data->toArray(),
				'html' => $this->view->render('index.phtml')
			));
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'status' => 'exception',
				'message' => $e->getMessage()
			));
		}
	}

}