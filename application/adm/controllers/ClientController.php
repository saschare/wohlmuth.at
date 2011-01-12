<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ClientController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'client',
				'action' => 'crud'
			)) && !Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'client',
				'action' => $this->getRequest()->getActionName()
			))) {
			throw new Exception('Access denied');
		}
	}

	public function indexAction() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function deleteclientAction() {

		$this->_helper->layout->disableLayout();

		Aitsu_Persistence_Clients :: factory($this->getRequest()->getParam('idclient'))->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function deletelanguageAction() {

		$this->_helper->layout->disableLayout();

		Aitsu_Persistence_Language :: factory($this->getRequest()->getParam('idlang'))->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function editclientAction() {

		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('idclient');

		$form = Aitsu_Forms :: factory('editclient', APPLICATION_PATH . '/adm/forms/client/client.ini');
		$form->title = Aitsu_Translate :: translate('Edit client');
		$form->url = $this->view->url();

		if (!empty ($id)) {
			$data = Aitsu_Persistence_Clients :: factory($id)->load()->toArray();
			$form->setValues($data);
		}

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				$values = $form->getValues();

				/*
				 * Persist the data.
				 */
				if (empty ($id)) {
					/*
					 * New client.
					 */
					unset ($values['idclient']);
					Aitsu_Persistence_Clients :: factory()->setValues($values)->save();
				} else {
					/*
					 * Update client.
					 */
					Aitsu_Persistence_Clients :: factory($id)->load()->setValues($values)->save();
				}

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

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function editlanguageAction() {

		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('idlang');

		$form = Aitsu_Forms :: factory('editlanguage', APPLICATION_PATH . '/adm/forms/client/language.ini');
		$form->title = Aitsu_Translate :: translate('Edit language');
		$form->url = $this->view->url();

		$clients = array ();
		foreach (Aitsu_Persistence_Clients :: getAsArray() as $key => $value) {
			$clients[] = (object) array (
				'value' => $key,
				'name' => $value
			);
		}
		$form->setOptions('idclient', $clients);

		if (!empty ($id)) {
			$data = Aitsu_Persistence_Language :: factory($id)->load()->toArray();
			$form->setValues($data);
		}

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				$values = $form->getValues();

				/*
				 * Persist the data.
				 */
				if (empty ($id)) {
					/*
					 * New language.
					 */
					unset ($values['idlang']);
					Aitsu_Persistence_Language :: factory()->setValues($values)->save();
				} else {
					/*
					 * Update language.
					 */
					Aitsu_Persistence_Language :: factory($id)->load()->setValues($values)->save();
				}

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

	/**
	 * @todo implement in version 2.1.x
	 */
	public function exportAction() {

		$this->_helper->viewRenderer->setNoRender(true);

		$this->view->clients = Aitsu_Persistence_Clients :: getAll();
		$this->view->languages = Aitsu_Persistence_Language :: getAll();

		echo $this->view->render('client/index.phtml');
	}

	/**
	 * @todo implement in version 2.1.x
	 */
	public function exportclientsAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'clients.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'clients',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Clients :: getAll()
		))->saveXML();
	}

	/**
	 * @todo implement in version 2.1.x
	 */
	public function exportlanguagesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$filename = 'languages.';
		$filename .= date('Y-m-d-H-i-s') . '.xml';

		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="' . $filename . '"');

		echo Aitsu_Filter_ToXml :: get(array (
			'info' => array (
				'type' => 'languages',
				'date' => date('Y-m-d H:i:s'),
				'system' => array (
					'HTTP_HOST' => $_SERVER['HTTP_HOST'],
					'SERVER_NAME' => $_SERVER['SERVER_NAME'],
					'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
					'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
				)
			),
			'data' => Aitsu_Persistence_Language :: getAll()
		))->saveXML();
	}

}