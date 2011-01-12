<?php

/**
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 */

class ConfigArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cbedb45-65b0-47ca-8a90-21c87f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'config',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Config'),
			'enabled' => true,
			'position' => self :: getPosition($idart, 'config'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		/*$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/config/forms/config.ini', 'edit'));
		$configSetOptions = array (
			'0' => ''
		);
		foreach (Aitsu_Persistence_ConfigSet :: getAsArray() as $key => $value) {
			$configSetOptions[$key] = $value;
		}
		$form->getElement('configsetid')->setMultiOptions($configSetOptions);
		$form->setAction($this->view->url());

		$data = Aitsu_Persistence_Article :: factory($id)->load();

		if ($this->getRequest()->getParam('loader')) {
			$formData = $data->toArray();
			$form->setValues($formData);
		}

		$this->view->pluginId = self :: ID;
		$this->view->id = $id;
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
			Aitsu_Event :: raise('backend.article.edit.save.start', array (
				'idart' => $id
			));
			$data->setValues($form->getValues());
			$data->save();
			$form->setValues($data->toArray());
			$this->_helper->json((object) array (
				'status' => 'success',
				'message' => Aitsu_Translate :: translate('Article configuration saved.'),
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