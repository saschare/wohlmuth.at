<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_Language extends Aitsu_Persistence_Abstract {

	protected $_id = null;
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

	public function load() {

		if ($this->_id == null || $this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select * from _lang where idlang = :id', array (
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

		Aitsu_Db :: put('_lang', 'idlang', $this->_data);
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	lang.*, ' .
		'	client.name as client ' .
		'from _lang as lang ' .
		'left join _clients as client on lang.idclient = client.idclient ' .
		'order by lang.name asc');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['idlang']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	lang.idlang, ' .
		'	lang.name as langname, ' .
		'	client.name as clientname ' .
		'from _lang as lang ' .
		'left join _clients as client on lang.idclient = client.idclient ' .
		'order by ' .
		'	lang.name asc, ' .
		'	client.name asc ');

		if (!$results) {
			return array ();
		}

		$return = array ();
		foreach ($results as $row) {
			$return[$row['idlang']] = $row['langname'] . ' (' . $row['clientname'] . ')';
		}

		return $return;
	}

	public static function getByClient($idclient) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _lang ' .
		'where idclient = :idclient ' .
		'order by name asc', array (
			':idclient' => $idclient
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['idlang']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _lang where idlang = :id', array (
			':id' => $this->_id
		));
	}

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public static function getStore($limit = null, $offset = null, $filters = null, $orders = null) {

		return Aitsu_Db :: filter('' .
		'select * from (' .
		'	select ' .
		'		lang.idlang, ' .
                '		lang.lngsort, ' .
		'		lang.name, ' .
		'		lang.longname, ' .
		'		client.name as client, ' .
		'		lang.locale, ' .
		'		lang.encoding, ' .
		'		lang.direction ' .
		'	from _lang lang' .
		'	left join _clients client on lang.idclient = client.idclient ' .
		') lang order by lngsort asc', $limit, $offset, $filters, $orders);
	}

	public static function getCurrentLangName() {

		try {
			return Aitsu_Db :: fetchOne('' .
			'select concat(client.name, \' / \', lang.name) ' .
			'from _lang lang ' .
			'left join _clients client on lang.idclient = client.idclient ' .
			'where lang.idlang = :idlang', array (
				':idlang' => Aitsu_Registry :: get()->session->currentLanguage
			));
		} catch (Exception $e) {
			return '';
		}
	}

}