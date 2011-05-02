<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Comment.php 18257 2010-08-23 08:42:55Z akm $}
 */

/*
CREATE TABLE  `con_comment` (
`commentid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`idartlang` INT UNSIGNED NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`email` VARCHAR( 255 ) NOT NULL ,
`ip` VARCHAR( 100 ) NOT NULL ,
`comment` TEXT NOT NULL ,
`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`deleted` TIMESTAMP NULL ,
INDEX (  `idartlang` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
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
			'email' => $_POST['email'],
			'comment' => $_POST['comment'],
			'name' => $_POST['name'],
			'idartlang' => Aitsu_Registry :: get()->env->idartlang
		);

		$data['uid'] = md5(implode('-', array (
			$data['ip'],
			$data['comment'],
			round(time() / 300)
		)));
		
		Aitsu_Form_Validation :: factory()->omit();
		unset($_POST['comment']);

		Aitsu_Db :: put('_comment', 'commentid', $data);
	}
}