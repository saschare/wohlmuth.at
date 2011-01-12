<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class DcArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cab1ded-59b0-40dd-a74c-0bfe7f000101';

	public function init() {

		header("Content-type: text/javascript");
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
		
		$form = Aitsu_Forms :: factory('pagedcmetadata', APPLICATION_PATH . '/plugins/article/dc/forms/meta.ini');
		$form->title = Aitsu_Translate :: translate('Dublin core');
		$form->url = $this->view->url(array (
			'plugin' => 'dc',
			'paction' => 'index'
		), 'aplugin');
		
		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		/*$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/dc/forms/meta.ini', 'edit'));
		$form->setAction($this->view->url());

		$data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();

		if ($this->getRequest()->getParam('loader')) {
			$form->setValues($data->toArray());
		}

		$this->view->pluginId = self :: ID;
		$this->view->form = $form;

		if ($this->getRequest()->getParam('loader')) {
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
		}*/
	}

}