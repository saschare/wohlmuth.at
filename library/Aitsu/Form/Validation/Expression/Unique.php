<?php


/**
 * @author Jens Hartlep
 * @author Andreas Kummer, w3concepts AG
 */

class Aitsu_Form_Validation_Expression_Unique implements Aitsu_Form_Validation_Expression_Interface {

	protected $args;

	protected function __construct($args) {

		$this->args = $args;

		if (!isset ($this->args['table']) || !preg_match('/^[a-zA-Z_]*$/s', $this->args['table'])) {
			throw new Aitsu_Form_Validation_Expression_Exception('The table parameter must not be empty and must not contain characters except a-z, A-Z and _.');
		}
		if (!isset ($this->args['row']) || !preg_match('/^[a-zA-Z_]*$/s', $this->args['row'])) {
			throw new Aitsu_Form_Validation_Expression_Exception('The row parameter must not be empty and must not contain characters except a-z, A-Z and _.');
		}
	}

	public static function init($args) {

		return new self($args);
	}

	public function isValid(& $value) {

		$table = $this->args['table'];
		$item = $this->args['row'];

		return Aitsu_Db :: fetchOne('' .
		'select count(*) from ' . $table . ' ' .
		'where ' . $item . ' = :value', array (
			':value' => $value
		)) == 0;
	}
}