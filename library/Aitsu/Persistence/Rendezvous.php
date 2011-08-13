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
		'select * from _rendezvous where idart = :id', array (
			':id' => $this->_id
		));

		if (!$this->_data) {
			$this->_data = array ();
		}

		$this->_data['idart'] = $this->_id;

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
	
	public function __isset($key) {
		
		return !empty($this->_data[$key]);
	}

	public function save() {

		if (empty ($this->_data) || (empty ($this->_data['starttime']) && empty ($this->_data['endtime']))) {
			$this->remove();
			return;
		}
trigger_error(var_export($this->_data, true));
		$this->starttime = empty ($this->starttime) ? null : $this->starttime;
		$this->endtime = empty ($this->endtime) ? null : $this->endtime;
		$this->until = empty ($this->until) ? null : $this->until;

		Aitsu_Db :: query('' .
		'insert into _rendezvous (idart, starttime, endtime, periodicity, until) ' .
		'values ' .
		'(:idart, :starttime, :endtime, :periodicity, :until) ' .
		'on duplicate key update ' .
		'	starttime = :starttime, ' .
		'	endtime = :endtime, ' .
		'	periodicity = :periodicity, ' .
		'	until = :until ', array (
			':idart' => $this->_id,
			':starttime' => $this->starttime,
			':endtime' => $this->endtime,
			':periodicity' => $this->periodicity,
			':until' => $this->until
		));
	}

	public function remove() {

		Aitsu_Db :: query('' .
		'delete from _rendezvous where idart = :id', array (
			':id' => $this->_id
		));
	}

}