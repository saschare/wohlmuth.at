<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class RendezvousArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e4671ec-61f4-4a7a-9ab4-0a367f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'rendezvous',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Rendez-vous'),
			'enabled' => true,
			'position' => self :: getPosition($idart, 'rendezvous'),
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
			$this->view->idart = $id;
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
				$data->setValues($form->getValues());
				$data->redirect = '0';
				$data->save();

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