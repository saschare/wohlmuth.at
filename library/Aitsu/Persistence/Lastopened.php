<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Lastopened.php 19214 2010-10-07 14:29:58Z akm $}
 */

class Aitsu_Persistence_Lastopened extends Aitsu_Persistence_Abstract {

	protected $_userid = null;
	protected $_idart = null;
	protected $_idlang = null;
	protected $_data = null;

	protected function __construct($idart) {

		$this->_userid = Aitsu_Adm_User :: getInstance()->getId();
		$this->_idart = $idart;
		$this->_idlang = Aitsu_Registry :: get()->session->currentLanguage;
	}

	public static function factory($id = null) {

		static $instance = array ();

		if (!isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		return $instance[$id];
	}

	public function load() {

		if ($this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.*, ' .
		'	catlang.* ' .
		'from _lastopened as lopen ' .
		'left join _art_lang as artlang on lopen.idart = artlang.idart and lopen.idlang = artlang.idlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _cat_lang as catlang on catart.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
		'where ' .
		'	lopen.userid = :userid ' .
		'	and artlang.idlang = :idlang ' .
		'order by ' .
		'	lopen.time desc ' .
		'limit 0, 100', array (
			':userid' => $this->_userid,
			':idlang' => $this->_idlang
		));

		return $this;
	}
	
	public function get($limit = 12) {
		
		return array_slice($this->_data, 0, $limit);
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

		Aitsu_Db :: query('' .
		'insert into _lastopened (idart, idlang, userid, time) ' .
		'values (:idart, :idlang, :userid, now()) ' .
		'on duplicate key update time = now()', array (
			':idart' => $this->_idart,
			':idlang' => $this->_idlang,
			':userid' => $this->_userid
		));

		return $this;
	}

	public function remove() {

		return $this;
	}

}