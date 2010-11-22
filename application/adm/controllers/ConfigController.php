<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ConfigController.php 19822 2010-11-11 13:00:12Z akm $}
 */

class ConfigController extends Zend_Controller_Action {

	public function indexAction() {

		$this->view->configs = Aitsu_Persistence_ConfigSet :: getByName('%', 200);
	}

	public function newAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/config/configset.ini', 'new'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_ConfigSet :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->configs = Aitsu_Persistence_ConfigSet :: getByName('%', 200);

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('config/overview.phtml');
	}

	public function deleteconfigsAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		Aitsu_Persistence_ConfigSet :: factory($this->getRequest()->getParam('id'))->remove();

		$this->view->configs = Aitsu_Persistence_ConfigSet :: getByName('%', 200);

		echo $this->view->render('config/overview.phtml');
	}

	public function editconfigsAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id') == null ? $this->getRequest()->getParam('configsetid') : $this->getRequest()->getParam('id');

		if ($this->getRequest()->getParam('cancel') != 1) {

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/config/configset.ini', 'edit'));
			$form->setAction($this->view->url());

			if (!$this->getRequest()->isPost()) {
				$form->setValues(Aitsu_Persistence_ConfigSet :: factory($id)->load()->toArray());
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('config/new.phtml');
				return;
			}

			$values = $form->getValues();

			Aitsu_Persistence_ConfigSet :: factory()->setValues($values)->save();
		} // else: form has been cancelled.

		$this->view->configs = Aitsu_Persistence_ConfigSet :: getByName('%', 200);

		echo $this->view->render('config/overview.phtml');
	}
}