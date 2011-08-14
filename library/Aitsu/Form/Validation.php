<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Form_Validation {

	protected $form;
	protected $method;
	protected $validators;
	protected $valid = null;
	protected $_omit = false;

	protected function __construct($form, $method) {

		$this->form = $form;
		$this->method = $method;
	}

	public static function factory($form = null, $method = 'post') {

		static $instance = array ();
		static $lastForm;

		if ($form == null) {
			$form = $lastForm;
		} else {
			$lastForm = $form;
		}

		if (!isset ($instance[$form])) {
			$instance[$form] = new self($form, $method);
		}

		return $instance[$form];
	}

	public function omit($omit = true) {

		$this->_omit = $omit;
	}

	public function setValidator($name, $validator, $args, $required = 0) {

		if ($required === true) {
			$required = 1;
		} else {
			$required = 0;
		}

		if (is_object($validator)) {
			$validatorObject = $validator;
		} else {
			include_once ('Aitsu/Form/Validation/Expression/' . $validator . '.php');
			$validatorObject = call_user_func_array(array (
				'Aitsu_Form_Validation_Expression_' . $validator,
				"init"
			), array (
				$args
			));
		}

		if (!isset ($this->validators[$name])) {
			$this->validators[$name] = array (
				'type' => array (
					$validatorObject
				),
				'required' => $required
			);
		} else {
			$this->validators[$name]['type'][] = $validatorObject;
		}

		return $this;
	}

	public function process($processor) {

		if (empty ($_POST)) {
			return false;
		}

		$dummy = null;
		foreach ($this->validators as $name => $validator) {
			if (!self :: isValid($name, $dummy)) {
				$this->valid = false;
				return false;
			}
		}

		if (Aitsu_Spam_Blacklist :: isSpam($_SERVER['REMOTE_ADDR'])) {
			return false;
		}

		$processor->process();

		return true;
	}

	public static function isValid($name, & $param) {

		$param = null;

		$i = self :: factory();

		if ($i->valid === true || empty ($_POST)) {
			return true;
		}

		if (!isset ($i->validators[$name])) {
			/*
			 * No checks to be made.
			 */
			return true;
		}

		if ($i->method == 'post') {
			$param = isset ($_POST[$name]) ? $_POST[$name] : null;
		}
		elseif ($i->method == 'get') {
			$param = isset ($_GET[$name]) ? $_GET[$name] : null;
		} else {
			$param = isset ($_REQUEST[$name]) ? $_REQUEST[$name] : null;
		}

		if (strlen(trim($param)) == 0) {
			$param = null;
		}

		if ($i->_omit) {
			return true;
		}

		if ($param == null && $i->validators[$name]['required'] === 0) {
			return true;
		}

		if ($param == null && $i->validators[$name]['required'] === 1) {
			return false;
		}

		foreach ($i->validators[$name]['type'] as $validator) {
			if (!$validator->isValid($param)) {
				return false;
			}
		}

		return true;
	}
}