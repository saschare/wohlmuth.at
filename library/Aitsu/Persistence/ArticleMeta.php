<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_ArticleMeta extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_idartlang = null;
	protected $_data = null;
	protected $_idlang = null;

	protected function __construct($id) {

		$this->_id = $id;
		$this->_idlang = Aitsu_Registry :: get()->session->currentLanguage;
		$this->_idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang from _art_lang ' .
		'where idart = :idart and idlang = :idlang', array (
			':idart' => $this->_id,
			':idlang' => $this->_idlang
		));
	}

	public static function factory($id = null) {

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
		'select ' .
		'	* ' .
		'from _art_meta ' .
		'where ' .
		'	idartlang = :idartlang', array (
			':idartlang' => $this->_idartlang
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

		Aitsu_Db :: startTransaction();

		try {
			$this->_data['idartlang'] = $this->_idartlang;

			if (Aitsu_Db :: fetchOne('select count(*) from _art_meta where idartlang = :idartlang', array (
					':idartlang' => $this->_idartlang
				)) == 0) {
				Aitsu_Db :: query('insert into _art_meta (idartlang) values (:idartlang)', array (
					':idartlang' => $this->_idartlang
				));
			}

			if (is_array($this->_data['robots'])) {
				$this->_data['robots'] = implode(', ', $this->_data['robots']);
			}

			Aitsu_Db :: put('_art_meta', 'idartlang', $this->_data);

			Aitsu_Db :: query('update _art_meta set date = null where date = :null', array (
				':null' => '0000-00-00'
			));
			Aitsu_Db :: query('update _art_meta set expires = null where expires = :null', array (
				':null' => '0000-00-00'
			));

			Aitsu_Persistence_Article :: touch($this->_idartlang);

			Aitsu_Db :: commit();

			return $this;
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

	}

	public function remove() {

		Aitsu_Db :: query('' .
		'delete from _art_meta ' .
		'where idartlang = :idartlang ', array (
			':idartlang' => $this->_idartlang
		));
	}

}