<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class ChannelArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e0b1ba0-a604-4ae5-aca2-16927f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'channel',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Channel'),
			'enabled' => self :: getPosition($idart, 'channel'),
			'position' => self :: getPosition($idart, 'channel'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('channels', APPLICATION_PATH . '/plugins/article/channel/forms/channel.ini');
		$form->title = Aitsu_Translate :: translate('Channels');
		$form->url = $this->view->url(array (
			'plugin' => 'channel',
			'paction' => 'index'
		), 'aplugin');

		/*$data = Aitsu_Persistence_Article :: factory($id)->load();
		$data->pagetitle = str_replace("'", "\\'", $data->pagetitle);
		$form->setValues($data->toArray());*/

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
				/*$data->setValues($form->getValues());
				$data->redirect = 1;
				$data->save();*/

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