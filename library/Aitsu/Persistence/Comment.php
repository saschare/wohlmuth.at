<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Persistence_Comment extends Aitsu_Persistence_Abstract implements Aitsu_Form_Processor_Interface {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public static function factory($id = null) {

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
		'select * from _comment where commentid = :id', array (
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

		$this->_data['uid'] = md5(implode('-', array (
			$this->_data['ip'],
			$this->_data['comment'],
			round(time() / 300)
		)));

		Aitsu_Db :: put('_comment', 'commentid', $this->_data);
	}

	public static function getByIdartlang($idartlang, $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _comment ' .
		'where ' .
		'	idartlang = :idartlang ' .
		'	and deleted is null ' .
		'order by created desc', array (
			':idartlang' => $idartlang
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$comment = new self($result['commentid']);
			$comment->_data = $result;
			$return[] = $comment;
		}

		return $return;
	}

	public function process() {

		$data = array (
			'ip' => isset ($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'email' => $this->email == null ? $_POST['email'] : $this->email,
			'comment' => $_POST['comment'],
			'title' => $_POST['title'],
			'name' => $this->name == null ? $_POST['name'] : $this->name,
			'idartlang' => Aitsu_Registry :: get()->env->idartlang
		);

		$data['uid'] = md5(implode('-', array (
			$data['ip'],
			$data['comment'],
			round(time() / 300)
		)));

		Aitsu_Form_Validation :: factory()->omit();
		unset ($_POST['comment']);
		
		if (Aitsu_Db :: fetchOne('' .
			'select count(*) from information_schema.columns ' .
			'where ' .
			'	table_schema = :schema ' .
			'	and table_name = :tableName ' .
			'	and column_name = :columnName ', array (
				':schema' => Aitsu_Registry :: get()->config->database->params->dbname,
				':tableName' => Aitsu_Registry :: get()->config->database->params->tblprefix . 'comment',
				':columnName' => 'properties'
			)) == 0) {
			Aitsu_Db :: query('' .
			'alter table _comment add title varchar(255) not null after ip');
		}

		Aitsu_Db :: put('_comment', 'commentid', $data);
	}
}