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
			'enabled' => self :: getPosition($idart, 'rendezvous'),
			'position' => self :: getPosition($idart, 'rendezvous'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');		
		$data = Aitsu_Persistence_Rendezvous :: factory($id)->load();

		$form = Aitsu_Forms :: factory('rendezvous', APPLICATION_PATH . '/plugins/article/rendezvous/forms/rendezvous.ini');
		$form->title = Aitsu_Translate :: translate('Rendez-vous');
		$form->url = $this->view->url(array (
			'plugin' => 'rendezvous',
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

		$options = array ();
		for ($i = 0; $i <= 28; $i++) {
			if ($i == 0) {
				$val = array (
					'name' => Aitsu_Translate :: translate('No recurrence'),
					'value' => $i
				);
			}
			elseif ($i == 1) {
				$val = array (
					'name' => Aitsu_Translate :: translate('Daily'),
					'value' => $i
				);
			}
			elseif ($i == 7) {
				$val = array (
					'name' => Aitsu_Translate :: translate('Weekly'),
					'value' => $i
				);
			}
			elseif ($i == 14) {
				$val = array (
					'name' => Aitsu_Translate :: translate('Every other week'),
					'value' => $i
				);
			}
			elseif ($i == 21) {
				$val = array (
					'name' => Aitsu_Translate :: translate('Every three weeks'),
					'value' => $i
				);
			}
			elseif ($i == 28) {
				$val = array (
					'name' => Aitsu_Translate :: translate('Every four weeks'),
					'value' => $i
				);
			} else {
				$val = array (
					'name' => $i,
					'value' => $i
				);
			}
			$options[] = (object) $val;
		}

		$form->setOptions('periodicity', $options);
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