<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Persistence_Rendezvous extends Aitsu_Persistence_Abstract {

	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public static function factory($id = null) {

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
		'select distinct rv.* ' .
		'from _rendezvous rv ' .
		'left join _art_lang artlang on rv.idart = artlang.idart ' .
		'where ' .
		'	idartlang = :id', array (
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

		if (empty ($this->_data) || (empty ($this->_data['starttime']) && empty ($this->_data['starttime']))) {
			$this->remove();
			return;
		}

		$this->_id = Aitsu_Db :: put('_rendezvous', 'idart', $this->_data);
	}

	public function remove() {

		Aitsu_Db :: query('' .
		'delete from _rendezvous ' .
		'where ' .
		'	idart = (select distinct idart from _art_lang where idartlang = :id) ', array (
			':id' => $this->_id
		));
	}

}