<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_Todo extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		return $instance[$id];
	}

	public function load() {

		if ($this->_id == null || $this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select * from _todo where todoid = :id', array (
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

		Aitsu_Db :: put('_todo', 'todoid', $this->_data);
	}

	public function remove() {

		Aitsu_Db :: query('delete from _todo where todoid = :id', array (
			':id' => $this->_id
		));
	}

	public function getStore($limit = null, $offset = null, $filters = null, $orders = null) {

		return Aitsu_Db :: filter('select * from _todo', $limit, $offset, $filters, $orders);
	}
}