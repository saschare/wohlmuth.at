<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Customer.php 19197 2010-10-06 16:46:55Z akm $}
 */

class Plugin_Tracking_Customer extends Aitsu_Persistence_Abstract {

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
		'select * from _tracking_customer where customerid = :id', array (
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

		$this->_id = Aitsu_Db :: put('_tracking_customer', 'customerid', $this->_data);

		return $this;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _tracking_customer where customerid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getByIdentifier($identifier = '%', $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _tracking_customer ' .
		'where ' .
		'	identifier like :identifier ' .
		'order by ' .
		'	identifier asc', array (
			':identifier' => $identifier
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['customerid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

}