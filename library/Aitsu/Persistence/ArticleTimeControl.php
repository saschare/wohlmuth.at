<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Persistence_ArticleTimeControl extends Aitsu_Persistence_Abstract {

	protected $_data = null;
	protected $_idlang = null;

	protected function __construct($id) {

		$this->_id = $id;

		if (Aitsu_Application_Status :: isEdit()) {
			$this->_idlang = Aitsu_Registry :: get()->session->currentLanguage;
		} else {
			$this->_idlang = Aitsu_Registry :: get()->env->idlang;
		}

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
		'select ' .
		'	t.* ' .
		'from _art_timecontrol t ' .
		'left join _art_lang a on t.idartlang = a.idartlang ' .
		'where ' .
		'	a.idart = :id ' .
		'	and a.idlang = :idlang', array (
			':id' => $this->_id,
			':idlang' => $this->_idlang
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
	
	public function __isset($key) {
		
		if (isset($this->_data[$key]) && $this->_data[$key] == 0) {
			return true;
		}
		
		return !empty($this->_data[$key]);
	}

	public function save() {

		Aitsu_Db :: query('' .
		'insert into _art_timecontrol ' .
		'(idartlang, timea, statusa, timeb, statusb) ' .
		'select ' .
		'	a.idartlang, ' .
		'	:timea timea, ' .
		'	:statusa statusa, ' .
		'	:timeb timeb, ' .
		'	:statusb statusb ' .
		'from _art_lang a ' .
		'where ' .
		'	a.idart = :id ' .
		'	and a.idlang = :idlang ' .
		'on duplicate key update ' .
		'	timea = :timea, ' .
		'	statusa = :statusa, ' .
		'	timeb = :timeb, ' .
		'	statusb = :statusb', array (
			':id' => $this->_id,
			':idlang' => $this->_idlang,
			':timea' => empty ($this->timea) ? null : $this->timea,
			':statusa' => empty($this->statusa) ? 0 : $this->statusa,
			':timeb' => empty ($this->timeb) ? null : $this->timeb,
			':statusb' => empty($this->statusb) ? 0 : $this->statusb
		));

		return $this;
	}

	public function remove() {
		/*
		 * Not implemented.
		 */
	}

}