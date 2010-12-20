<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_ConfigSet extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance = new self($id);
		}

		return $instance;
	}

	public function load() {

		if ($this->_id == null || $this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select * from _configset where configsetid = :id', array (
			':id' => $this->_id
		));

		return $this;
	}

	public function __get($key) {

		if ($this->_data === null) {
			$this->load();
		}

		if (!isset ($this->_data[$key])) {
			return null;
		}

		return stripslashes($this->_data[$key]);
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function save() {

		if (empty ($this->_data)) {
			return;
		}

		$this->_id = Aitsu_Db :: put('_configset', 'configsetid', $this->_data);
	}

	public function remove() {

		if ($this->_id == 1) {
			/*
			 * The configuration with ID 1 is the default configuration and
			 * must not be deleted.
			 */
			return;
		}

		Aitsu_Db :: query('delete from _configset where configsetid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getByName($name = '%', $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _configset ' .
		'where ' .
		'	identifier like :name ' .
		'order by ' .
		'	identifier asc', array (
			':name' => $name
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['configsetid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$configSets = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	configsetid, ' .
		'	identifier ' .
		'from _configset order by identifier asc');

		if (!$configSets) {
			return array ();
		}

		$return = array ();

		foreach ($configSets as $set) {
			$return[$set['configsetid']] = $set['identifier'];
		}

		return $return;
	}
}