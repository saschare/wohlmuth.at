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
		$data = Aitsu_Persistence_Article :: factory($id)->load();

		$form = Aitsu_Forms :: factory('pageconfiguration', APPLICATION_PATH . '/plugins/article/config/forms/config.ini');
		$form->title = Aitsu_Translate :: translate('Configuration');
		$form->url = $this->view->url(array (
			'plugin' => 'config',
			'paction' => 'index'
		), 'aplugin');

		$options = array (
			(object) array (
				'name' => '[inherit]',
				'value' => null
			)
		);
		$configSets = Aitsu_Persistence_ConfigSet :: getAsArray();
		foreach ($configSets as $key => $value) {
			$options[] = (object) array (
				'name' => $value,
				'value' => $key
			);
		}

		$form->setOptions('configsetid', $options);
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
	}
}