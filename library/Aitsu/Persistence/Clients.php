<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Clients.php 18639 2010-09-09 11:23:10Z akm $}
 */

class Aitsu_Persistence_Clients extends Aitsu_Persistence_Abstract {

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
		'select * from _clients where idclient = :id', array (
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

		$this->_data['lastmodified'] = date('Y-m-d H:i:s');

		if ($this->_id == null) {
			$this->_data['created'] = date('Y-m-d H:i:s');
		}

		Aitsu_Db :: put('_clients', 'idclient', $this->_data);
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _clients ' .
		'order by name asc');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['idclient']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _clients ' .
		'order by ' .
		'	name asc ');

		if (!$results) {
			return array ();
		}

		$return = array ();
		foreach ($results as $row) {
			$return[$row['idclient']] = $row['name'];
		}

		return $return;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _clients where idclient = :id', array (
			':id' => $this->_id
		));
	}

	public function getPotentialConfigs() {

		$return = array ();

		$configs = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/configs/clients', '*.ini');

		foreach ($configs as $config) {
			$config = basename($config, '.ini');
			$return[$config] = $config;
		}

		return $return;
	}
}