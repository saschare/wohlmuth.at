<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class TimecontrolArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e9fe984-4d3c-47a1-8e3f-0c867f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'timecontrol',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Time control'),
			'enabled' => self :: getPosition($idart, 'timecontrol'),
			'position' => self :: getPosition($idart, 'timecontrol'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');
		$data = Aitsu_Persistence_ArticleTimeControl :: factory($id)->load();
		$data->idart = $id;

		$form = Aitsu_Forms :: factory('timecontrol', APPLICATION_PATH . '/plugins/article/timecontrol/forms/form.ini');
		$form->title = Aitsu_Translate :: translate('Time control');
		$form->url = $this->view->url(array (
			'plugin' => 'timecontrol',
			'paction' => 'index'
		), 'aplugin');
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
				Aitsu_Persistence_ArticleTimeControl :: factory($id)->setValues($form->getValues())->save();

				$this->_helper->json((object) array (
					'success' => true
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