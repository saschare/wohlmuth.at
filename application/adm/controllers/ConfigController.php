<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class ConfigController extends Zend_Controller_Action {

	public function init() {

		$this->_helper->layout->disableLayout();

		$this->_filter = Aitsu_Util_ExtJs :: encodeFilters($this->getRequest()->getParam('filter'));
	}

	public function indexAction() {
	}

	public function storeAction() {

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_ConfigSet :: getStore(100, 0, $this->_filter)
		));
	}

	public function deleteAction() {

		$this->_helper->layout->disableLayout();

		Aitsu_Persistence_ConfigSet :: factory($this->getRequest()->getParam('configsetid'))->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function editAction() {

		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('configsetid');

		$form = Aitsu_Forms :: factory('editconfigset', APPLICATION_PATH . '/adm/forms/config/config.ini');
		$form->title = Aitsu_Translate :: translate('Edit config set');
		$form->url = $this->view->url();

		if (!empty ($id)) {
			$data = Aitsu_Persistence_ConfigSet :: factory($id)->load()->toArray();
			$data['config'] = str_replace("\r", '', $data['config']);
			$data['config'] = str_replace("\n", '\n', $data['config']);
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
					 * New config set.
					 */
					unset ($values['configsetid']);
					Aitsu_Persistence_ConfigSet :: factory()->setValues($values)->save();
				} else {
					/*
					 * Update config set.
					 */
					Aitsu_Persistence_ConfigSet :: factory($id)->load()->setValues($values)->save();
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
}