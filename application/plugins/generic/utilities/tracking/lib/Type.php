<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Type.php 19209 2010-10-06 20:52:33Z akm $}
 */

class Plugin_Tracking_Type extends Aitsu_Persistence_Abstract {

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
		'select * from _tracking_type where typeid = :id', array (
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
			return $this;
		}

		$this->_id = Aitsu_Db :: put('_tracking_type', 'typeid', $this->_data);

		return $this;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _tracking_type where typeid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _tracking_type ' .
		'order by ' .
		'	identifier asc');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['typeid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _tracking_type ' .
		'order by ' .
		'	identifier asc ');

		if (!$results) {
			return array ();
		}

		$return = array ();
		foreach ($results as $row) {
			$return[$row['typeid']] = $row['identifier'];
		}

		return $return;
	}

}