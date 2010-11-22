<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: CatFavorite.php 19214 2010-10-07 14:29:58Z akm $}
 */

class Aitsu_Persistence_CatFavorite extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_userid = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
		$this->_userid = Aitsu_Adm_User :: getInstance()->getId();
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
		'select * from _cat_favorites ' .
		'where ' .
		'	userid = :userid ' .
		'	and idcat = :idcat', array (
			':idcat' => $this->_id,
			':userid' => $this->_userid
		));

		return $this;
	}
	
	public function isInFavorites() {
		
		if (empty($this->_data)) {
			return false;
		}
		
		return true;
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

		if (empty ($this->_id)) {
			return;
		}

		Aitsu_Db :: query('' .
		'insert into _cat_favorites (idcat, userid) values (:idcat, :userid) ' .
		'on duplicate key update idcat = idcat ', array (
			':idcat' => $this->_id,
			':userid' => $this->_userid
		));
	}

	public function remove() {

		Aitsu_Db :: query('' .
				'delete from _cat_favorites ' .
				'where ' .
				'	userid = :userid ' .
				'	and idcat = :idcat', array (
			':idcat' => $this->_id,
			':userid' => $this->_userid
		));
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	catlang.* ' .
		'from _cat_favorites as favcat ' .
		'left join _cat_lang as catlang on favcat.idcat = catlang.idcat ' .
		'where ' .
		'	favcat.userid = :userid ' .
		'	and catlang.idlang = :idlang ' .
		'order by ' .
		'	catlang.name asc', array (
			':userid' => Aitsu_Adm_User :: getInstance()->getId(),
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['idcat']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

}