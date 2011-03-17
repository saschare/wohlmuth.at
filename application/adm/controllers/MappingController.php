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

	public function mappingsAction() {

		$data = array ();

		try {
			$mappings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mapping.ini', 'map');

			foreach ($mappings->item as $id => $item) {
				$data[] = (object) array (
					'id' => $id,
					'name' => $item->name,
					'env' => $item->env
				);
			}
		} catch (Exception $e) {
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
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

	/**
	 * @since 2.1.0.0 - 27.12.2010
	 */
	public function editAction() {

		$this->_helper->layout->disableLayout();

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

		$form = Aitsu_Forms :: factory('editmapping', APPLICATION_PATH . '/adm/forms/mapping/mapping.ini');
		$form->title = Aitsu_Translate :: translate('Edit mapping');
		$form->url = $this->view->url();

		$positions = array ();
		foreach ($config->item as $id => $item) {
			$positions[] = (object) array (
				'value' => $id,
				'name' => $this->view->translate('before') . ': ' . $item->name
			);
		}
		$positions[] = (object) array (
			'value' => $id +1,
			'name' => $this->view->translate('Last')
		);
		$form->setOptions('pos', $positions);

		$id = $this->getRequest()->getParam('id');
		if (!empty ($id)) {
			$item = $config->item-> $id;
			$form->setValues(array_merge($item->toArray(), array (
				'id' => $id,
				'conditions' => implode('\n', $item->conditions->toArray()),
				'pos' => $id
			)));
		}

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				/*
				 * Persist the data.
				 */
				$this->_saveMapping($form->getValues(), $config);

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

	public function deleteAction() {

		$this->_helper->layout->disableLayout();

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

		$this->_helper->json((object) array (
			'success' => true
		));
	}
}