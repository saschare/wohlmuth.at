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
		
		$data = Aitsu_Persistence_ArticleMeta :: factory($id)->load();
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
	}

}