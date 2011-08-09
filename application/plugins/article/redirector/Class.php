<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class RedirectorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cc155c9-18c0-42eb-97b9-0ab77f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		if (!self :: getPosition($idart, 'redirector') && Aitsu_Db :: fetchOne('' .
			'select redirect from _art_lang ' .
			'where ' .
			'	idart = :idart ' .
			'	and idlang = :idlang', array (
				':idart' => $idart,
				':idlang' => Aitsu_Registry :: get()->session->currentLanguage
			)) == 1) {
			/*
			 * Make sure that redirect is set to 0.
			 */
			Aitsu_Db :: query('' .
			'update _art_lang set redirect = 0 ' .
			'where ' .
			'	idart = :idart ' .
			'	and idlang = :idlang', array (
				':idart' => $idart,
				':idlang' => Aitsu_Registry :: get()->session->currentLanguage
			));
		}

		return (object) array (
			'name' => 'redirector',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Redirect'),
			'enabled' => self :: getPosition($idart, 'redirector'),
			'position' => self :: getPosition($idart, 'redirector'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('redirector', APPLICATION_PATH . '/plugins/article/redirector/forms/redirector.ini');
		$form->title = Aitsu_Translate :: translate('Redirection');
		$form->url = $this->view->url(array (
			'plugin' => 'redirector',
			'paction' => 'index'
		), 'aplugin');

		$data = Aitsu_Persistence_Article :: factory($id)->load();
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
				$data->setValues($form->getValues());
				$data->redirect = 1;
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