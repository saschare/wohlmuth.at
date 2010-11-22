<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Tag.php 19364 2010-10-18 09:53:44Z akm $}
 */

class Aitsu_Persistence_Tag extends Aitsu_Persistence_Abstract {

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
		'select * from _tag where tagid = :id', array (
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

		$this->_id = Aitsu_Db :: put('_tag', 'tagid', $this->_data);

	}

	public function remove() {

		Aitsu_Db :: query('delete from _tag where tagid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getByName($name = '%', $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _tag ' .
		'where ' .
		'	tag like :tag ' .
		'order by ' .
		'	tag asc', array (
			':tag' => $name
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['tagid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

}