<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Forms {

	protected $_uid;
	protected $_id;
	protected $_config = null;
	protected $_errors = null;

	public $title = '';
	public $url = '';

	protected function __construct($id, $ini) {

		$this->_id = $id;
		$this->_uid = uniqid($id . '-');

		if ($ini != null) {
			if (is_object($ini)) {
				$this->_config = $ini;
			} else {
				$this->_config = new Zend_Config_Ini($ini, null, array (
					'allowModifications' => true
				));
			}
		}
	}

	public function factory($id, $ini = null) {

		static $instance = array ();

		if (!isset ($instance[$id])) {
			$instance[$id] = new self($id, $ini);
		}

		return $instance[$id];
	}

	public function isValid() {

		$errors = array ();
		$this->_isValid($this->_config, $errors);

		$this->_errors = $errors;

		return count($errors) == 0;
	}

	public function getErrors() {

		$errors = array ();

		foreach ($this->_errors as $fieldname => $error) {
			$errors[] = (object) array (
				'id' => $fieldname,
				'msg' => $error
			);
		}

		return $errors;
	}

	protected function _isValid(& $config, & $target) {

		if (!is_object($config)) {
			return;
		}

		if (isset ($config->field)) {
			foreach ($config->field as $key => $value) {
				if (isset ($value->validator)) {
					foreach ($value->validator as $validator) {
						if (class_exists('Aitsu_Forms_Validator_' . ucfirst($validator->type))) {
							$val = call_user_func(array (
								'Aitsu_Forms_Validator_' . ucfirst($validator->type),
								'factory'
							), isset ($validator->param) ? $validator->param->toArray() : null);
							if (!$val->isValid($_REQUEST[$key])) {
								$target[$key] = $val->getMessage();
							}
						}
					}
				}
			}
		}

		foreach ($config as $key => $obj) {
			$this->_isValid($obj, $target);
		}
	}

	public function render($type) {

		return call_user_func(array (
			'Aitsu_Forms_Renderer_' . $type,
			'render'
		), $this);
	}

	public function getParams() {

		return $this->_config->form;
	}

	public function getGroups() {

		return $this->_config->group;
	}

	public function getButtons() {

		return $this->_config->button;
	}

	public function getUid() {

		return $this->_uid;
	}

	public function setValues(array $values) {

		foreach ($values as $key => $value) {
			$this->setValue($key, $value);
		}
	}

	public function setValue($fieldname, $value) {

		$this->_setVal($this->_config, 'field', 'value', $fieldname, $value);
	}

	protected function _setVal($config, $toField, $valKey, & $fieldname, & $value) {

		if (!is_object($config)) {
			return false;
		}

		if (isset ($config-> $toField-> $fieldname)) {
			$config-> $toField-> $fieldname-> $valKey = $value;
			return true;
		}

		foreach ($config as $key => $obj) {
			if ($this->_setVal($obj, $toField, $valKey, $fieldname, $value)) {
				return true;
			}
		}

		return false;
	}

	public function setOptions($fieldname, array $options) {

		$this->_setVal($this->_config, 'field', 'option', $fieldname, $options);
	}

	public function getValues() {

		$values = array ();
		$this->_getValues($this->_config, $values);

		return $values;
	}

	protected function _getValues(& $config, & $target) {

		if (!is_object($config)) {
			return;
		}

		if (isset ($config->field)) {
			foreach ($config->field as $key => $value) {
				$target[$key] = isset ($_REQUEST[$key]) ? $_REQUEST[$key] : null;
			}
			return;
		}

		foreach ($config as $key => $obj) {
			$this->_getValues($obj, $target);
		}
	}
}