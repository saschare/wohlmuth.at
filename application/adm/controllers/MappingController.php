<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class MappingController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'mapping',
				'action' => 'crud'
			)) && !Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'mapping',
				'action' => $this->getRequest()->getActionName()
			))) {
			throw new Exception('Access denied');
		}
		
		$this->_helper->layout->disableLayout();
	}

	public function indexAction() {
		
		header("Content-type: text/javascript");

		if (!file_exists(APPLICATION_PATH . '/configs/mapping.ini')) {
			$this->view->error = 1;
		}
		elseif (!is_writable(APPLICATION_PATH . '/configs/mapping.ini')) {
			$this->view->error = 2;
		}

		try {
			$this->view->mappings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');
		} catch (Exception $e) {
			$this->view->mappings = array ();
		}
	}

	public function newAction() {

		$this->_helper->layout->disableLayout();

		if ($this->getRequest()->getParam('cancel') != 1) {

			try {
				$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map', array (
					'allowModifications' => true
				));
			} catch (Exception $e) {
				$config = new Zend_Config(array (), true);
			}

			if (empty ($config->item)) {
				$config->item = array ();
			}

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/mapping/mapping.ini', 'new'));
			$form->setAction($this->view->url());

			// $form->getElement('client')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());

			$posOptions = array ();
			$posOptions[] = $this->view->translate('First');
			foreach ($config->item as $id => $item) {
				$posOptions[] = $this->view->translate('before') . ': ' . $item->name;
			}
			$posOptions[] = $this->view->translate('Last');
			$form->getElement('pos')->setMultiOptions($posOptions);

			if (!$this->getRequest()->isPost()) {
				$form->setValues(array (
					'pos' => count($posOptions) - 1
				));
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				return;
			}

			$this->_saveMapping($form->getValues(), $config);

		} // else: form has been cancelled.

		try {
			$this->view->mappings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');
		} catch (Exception $e) {
			$this->view->mappings = array ();
		}

		$this->_helper->viewRenderer->setNoRender(true);
		echo $this->view->render('mapping/list.phtml');
	}

	protected function _saveMapping($values, $config) {

		if (is_numeric($values)) {
			$id = $values;
			$values = array ();
			$values['id'] = $id;
			$values['pos'] = -1;
		}

		$newConfig = new Zend_Config(array (), true);

		$newConfig->map = array ();
		$env = $newConfig->map;
		$env->item = array ();

		$counter = 1;
		if ($values['pos'] == 0) {
			$this->_addItem($env, $counter, $values);
			$counter++;
		}
		foreach ($config->item as $id => $item) {
			if ($values['pos'] == $id) {
				$this->_addItem($env, $counter, $values);
				$counter++;
			}
			if (!isset ($values['id']) || $values['id'] != $id) {
				$this->_addItem($env, $counter, $item);
				$counter++;
			}
		}
		if ($values['pos'] > count($config->item->toArray())) {
			$this->_addItem($env, $counter, $values);
		}

		$writer = new Zend_Config_Writer_Ini(array (
			'config' => $newConfig,
			'filename' => APPLICATION_PATH . '/configs/mapping.ini'
		));
		$writer->write();

	}

	protected function _addItem($env, $id, $values) {

		if (!is_object($values)) {
			$values = (object) $values;
		}

		$env->item-> {
			$id }
		= array ();

		$env->item-> {
			$id }
		->name = $values->name;
		$env->item-> {
			$id }
		->client = $values->client;
		$env->item-> {
			$id }
		->env = $values->env;
		$env->item-> {
			$id }
		->conditions = $this->_splitConditions($values->conditions);
	}

	protected function _splitConditions($cond) {

		if (is_object($cond)) {
			return $cond;
		}

		$return = array ();
		$cond = explode("\n", $cond);

		foreach ($cond as $condition) {
			if (!empty ($condition)) {
				$return[] = $condition;
			}
		}

		return $return;
	}

	public function editmappingAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		if ($this->getRequest()->getParam('cancel') != 1) {

			try {
				$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map', array (
					'allowModifications' => true
				));
			} catch (Exception $e) {
				$config = new Zend_Config(array (), true);
			}

			if (empty ($config->item)) {
				$config->item = array ();
			}

			$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/adm/forms/mapping/mapping.ini', 'edit'));
			$form->setAction($this->view->url());

			// $form->getElement('client')->setMultiOptions(Aitsu_Persistence_Clients :: getAsArray());

			$posOptions = array ();
			$posOptions[] = $this->view->translate('First');
			foreach ($config->item as $id => $item) {
				$posOptions[] = $this->view->translate('before') . ': ' . $item->name;
			}
			$posOptions[] = $this->view->translate('Last');
			$form->getElement('pos')->setMultiOptions($posOptions);

			if (!$this->getRequest()->isPost()) {
				$id = $this->getRequest()->getParam('id');
				$item = $config->item-> {
					$id };
				$form->setValues(array_merge($item->toArray(), array (
					'id' => $id,
					'conditions' => implode("\n", $item->conditions->toArray()),
					'pos' => $id
				)));
			}

			if (!$this->getRequest()->isPost() || !$form->isValid($_POST)) {
				$this->view->form = $form;
				echo $this->view->render('mapping/new.phtml');
				return;
			}

			$this->_saveMapping($form->getValues(), $config);

		} // else: form has been cancelled.

		try {
			$this->view->mappings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');
		} catch (Exception $e) {
			$this->view->mappings = array ();
		}

		echo $this->view->render('mapping/list.phtml');
	}

	public function deletemappingAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		try {
			$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map', array (
				'allowModifications' => true
			));
		} catch (Exception $e) {
			$config = new Zend_Config(array (), true);
		}

		if (empty ($config->item)) {
			$config->item = array ();
		}

		$this->_saveMapping($this->getRequest()->getParam('id'), $config);

		try {
			$this->view->mappings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');
		} catch (Exception $e) {
			$this->view->mappings = array ();
		}

		echo $this->view->render('mapping/list.phtml');
	}
}