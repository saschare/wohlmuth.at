<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class MetaArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4ca98c9e-85ec-4228-8171-12657f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'meta',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Meta'),
			'enabled' => self :: getPosition($idart, 'meta'),
			'position' => self :: getPosition($idart, 'meta'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('pagemetadata', APPLICATION_PATH . '/plugins/article/meta/forms/meta.ini');
		$form->title = Aitsu_Translate :: translate('Meta data');
		$form->url = $this->view->url(array (
			'plugin' => 'meta',
			'paction' => 'index'
		), 'aplugin');

		$data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();
		$data->robots = explode(', ', $data->robots);
		$data->idart = $id;
		$form->setValues($data->toArray());

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				Aitsu_Event :: raise('backend.article.edit.save.start', array (
					'idart' => $id
				));			

				/*
				 * Persist the data.
				 */
				$data->setValues($form->getValues())->save();

				$this->_helper->json((object) array (
					'success' => true,
					'data' => (object) $data->toArray()
				));
			} else {
				$this->_helper->json((object) array (
					'success' => false,
					'errors' => $form->getErrors()
				));
			}
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'exception' => true,
				'message' => $e->getMessage()
			));
		}

		/*$id = $this->getRequest()->getParam('idart');
		
		$robotsOptions = array (
			'index' => 'index',
			'follow' => 'follow',
			'noindex' => 'noindex',
			'nofollow' => 'nofollow'
		);
		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/meta/forms/meta.ini', 'edit'));
		$form->setAction($this->view->url());
		$form->getElement('robots')->setMultiOptions($robotsOptions);
		
		$data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();
		
		if ($this->getRequest()->getParam('loader')) {
			$formData = $data->toArray();
			$formData['robots'] = explode(', ', $formData['robots']);
			$form->setValues($formData);
		}
		
		$data->robots = explode(', ', $data->robots);
		
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
			$form->getElement('robots')->setMultiOptions($robotsOptions);
			$formData = $data->toArray();
			$formData['robots'] = explode(', ', $formData['robots']);
			$form->setValues($formData);
			$this->_helper->json((object) array (
				'status' => 'success',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('Meta data saved.'),
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