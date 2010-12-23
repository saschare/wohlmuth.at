<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_Resource extends Aitsu_Persistence_Abstract {

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
		'select * from _acl_resource where resourceid = :id', array (
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

		$this->_id = Aitsu_Db :: put('_acl_resource', 'resourceid', $this->_data);
	}

	public function remove() {

		Aitsu_Db :: query('' .
		'delete from _acl_resource ' .
		'where ' .
		'	resourceid = :id ' .
		'	and removeable = 1', array (
			':id' => $this->_id
		));
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _acl_resource ' .
		'order by name asc');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['resourceid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$return = array (
			1 => 'Root'
		);

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _acl_resource ' .
		'where name != :root ' .
		'order by name', array (
			':root' => 'Root'
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $row) {
			$return[$row['resourceid']] = $row['name'];
		}

		return $return;
	}
	
	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function getStore($limit = null, $offset = null, $filters = null, $orders = null) {

		return Aitsu_Db :: filter('select * from _acl_resource', $limit, $offset, $filters, $orders);
	}

}