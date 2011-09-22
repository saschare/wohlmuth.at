<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class GooglelocatorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e7b5a8c-7ac8-4f3e-8a7f-12057f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'googlelocator',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Google Locator'),
			'enabled' => self :: getPosition($idart, 'googlelocator'),
			'position' => self :: getPosition($idart, 'googlelocator'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('googlelocator', APPLICATION_PATH . '/plugins/article/googlelocator/forms/googlelocator.ini');
		$form->title = Aitsu_Translate :: translate('Google Locator');

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
				// $data->setValues($form->getValues())->save();

				/*$this->_helper->json((object) array (
					'success' => true,
					'data' => (object) $data->toArray()
				));*/
				$this->_helper->json((object) array (
					'success' => true,
					'data' => (object) array()
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