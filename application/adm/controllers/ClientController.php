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

	public function newclientAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/client/client.ini', 'new'));
			$form->setAction($this->view->url());

			$form->getElement('config')->setMultiOptions(Aitsu_Persistence_Clients :: getPotentialConfigs());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Clients :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->clients = Aitsu_Persistence_Clients :: getAll();

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('client/clientlist.phtml');
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

	public function deletelanguageAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_Language :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->languages = Aitsu_Persistence_Language :: getAll();
		echo $this->view->render('client/languagelist.phtml');
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

		if (!$this->getRequest()->isPost()) {
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

		/*$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('idclient') : $this->getRequest()->getParam('id');
		
		if ($this->getRequest()->getParam('cancel') != 1) {
		
			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/client/client.ini', 'edit'));
			$form->setAction($this->view->url());
		
			$form->getElement('config')->setMultiOptions(Aitsu_Persistence_Clients :: getPotentialConfigs());
		
			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_Clients :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}
		
			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('client/newclient.phtml');
				return;
			}
		
			$values = $form->getValues();
		
			Aitsu_Persistence_Clients :: factory()->setValues($values)->save();
		} // else: form has been cancelled.
		
		$this->view->clients = Aitsu_Persistence_Clients :: getAll();
		
		echo $this->view->render('client/clientlist.phtml');*/
	}

	public function editlanguageAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('idlang') : $this->getRequest()->getParam('id');

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/client/language.ini', 'edit'));
			$form->setAction($this->view->url());

			$form->getElement('idclient')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_Language :: factory($this->getRequest()->getParam('id'))->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('client/newlanguage.phtml');
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Language :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->languages = Aitsu_Persistence_Language :: getAll();

		echo $this->view->render('client/languagelist.phtml');
	}

	public function newlanguageAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/client/language.ini', 'new'));
			$form->setAction($this->view->url());

			$form->getElement('idclient')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_Language :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->languages = Aitsu_Persistence_Language :: getAll();

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('client/languagelist.phtml');
	}

	public function exportAction() {

		$this->_helper->viewRenderer->setNoRender(true);

		$this->view->clients = Aitsu_Persistence_Clients :: getAll();
		$this->view->languages = Aitsu_Persistence_Language :: getAll();

		echo $this->view->render('client/index.phtml');
	}

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