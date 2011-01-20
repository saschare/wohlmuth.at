<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_Rating extends Aitsu_Persistence_Abstract {

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
		'select * from _rating where idartlang = :id', array (
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

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function save() {

		/*
		 * Not implemented.
		 */

		return $this;
	}

	public function remove() {

		/*
		 * Not implemented.
		 */
	}

	public static function rate($rating) {

		if (!is_numeric($rating)) {
			return;
		}

		$idartlang = Aitsu_Registry :: get()->env->idartlang;
		$ip = $_SERVER['REMOTE_ADDR'];

		try {
			Aitsu_Db :: startTransaction();

			Aitsu_Db :: query('' .
			'insert into _ratings ' .
			'(idartlang, rated, ip, rate) ' .
			'values ' .
			'(:idartlang, now(), :ip, :rate) ' .
			'on duplicate key update rate = :rate', array (
				':idartlang' => $idartlang,
				':ip' => $ip,
				':rate' => $rating
			));

			Aitsu_Db :: query('' .
			'delete from _rating where idartlang = :idartlang', array (
				':idartlang' => $idartlang
			));

			Aitsu_Db :: query('' .
			'insert into _rating ' .
			'(idartlang, rating, votes) ' .
			'select ' .
			'	idartlang, ' .
			'	agv(rate) as rating, ' .
			'	count(*) as votes ' .
			'from _ratings ' .
			'where idartlang = :idartlang ' .
			'group by idartlang', array (
				':idartlang' => $idartlang
			));

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
		}
	}
}