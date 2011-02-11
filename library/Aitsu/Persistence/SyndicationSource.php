<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_SyndicationSource extends Aitsu_Persistence_Abstract {

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

	public function load($reload = false) {

		if (!$reload && ($this->_id == null || $this->_data !== null)) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	* ' .
		'from _syndication_source ' .
		'where ' .
		'	sourceid = :id ', array (
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

		if (is_array($this->_data[$key])) {
			return $this->_data[$key];
		}

		return stripslashes($this->_data[$key]);
	}

	public function getData() {

		return $this->_data;
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

		try {
			Aitsu_Db :: startTransaction();

			if (strlen($this->_data['secret']) != 32) {
				$this->_data['secret'] = md5($this->_data['secret']);
			}

			$this->_data['sourceid'] = Aitsu_Db :: put('_syndication_source', 'sourceid', $this->_data);

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

		return $this;
	}

	public function remove() {

		Aitsu_Db :: startTransaction();

		try {
			Aitsu_Db :: query('' .
			'delete from _syndication_source ' .
			'where ' .
			'	sourceid = :id ', array (
				':id' => $this->_id
			));

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}
	}

}