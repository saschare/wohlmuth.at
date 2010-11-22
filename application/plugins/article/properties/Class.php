<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19916 2010-11-17 12:40:58Z akm $}
 */

class PropertiesArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4ca9a84f-d2c0-4011-8903-177a7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'properties',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Properties'),
			'enabled' => self :: getPosition($idart, 'properties'),
			'position' => self :: getPosition($idart, 'properties'),
			'id' => self :: ID
		);
	}
	
	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/properties/forms/properties.ini', 'edit'));
		$form->setAction($this->view->url());

		$data = Aitsu_Persistence_Article :: factory($id)->load();
		$data->redirect = 0;

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
			Aitsu_Event :: raise('backend.article.edit.save.start', array (
				'idart' => $id
			));
			$data->setValues($form->getValues())->save();
			$form->setValues($data->toArray());
			$this->_helper->json((object) array (
				'status' => 'success',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('Properties saved.'),
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