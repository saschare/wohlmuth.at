<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/*
 * Important notice: This class has not yet been finished. In its current
 * state it should be used solely for the editing of media tags. It will
 * be finished in a coming release and will then replace the Aitsu_Core_File
 * class.
 */

class Aitsu_Persistence_File extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_idlang = null;
	protected $_tags = null;

	protected function __construct($id) {

		$this->_id = $id;
		$this->_idlang = Aitsu_Registry :: get()->env->idlang;
	}

	public function factory($id = null, $idlang = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		if (!is_null($idlang)) {
			$instance[$id]->_idlang = $idlang;
		}

		return $instance[$id];
	}

	public function load($reload = false) {

		if (!$reload && ($this->_id == null || $this->_data !== null)) {
			return $this;
		}

		/*
		 * Not yet implemented.
		 */

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

	public function getData() {

		return $this->_data;
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function save() {

		/*
		 * Not yet implemented.
		 */

		return $this;
	}

	public function remove() {

		/*
		 * Not yet implemented.
		 */
	}

	public function addTag($token, $value) {

		$value = empty ($value) ? null : $value;

		Aitsu_Db :: startTransaction();

		try {
			$tagid = Aitsu_Db :: fetchOne('' .
			'select mediatagid from _media_tag where tag = :tag', array (
				':tag' => $token
			));

			if (!$tagid) {
				$tagid = Aitsu_Db :: query('' .
				'insert into _media_tag (tag) values (:tag)', array (
					':tag' => $token
				))->getLastInsertId();
			}

			Aitsu_Db :: query('' .
			'insert into _media_tags (mediatagid, mediaid, val) values (:tagid, :mediaid, :value) ' .
			'on duplicate key update val = :value', array (
				':mediaid' => $this->_id,
				':tagid' => $tagid,
				':value' => $value
			));

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}
	}

	public function getTags() {

		if ($this->_tags !== null) {
			return $this->_tags;
		}

		$this->_tags = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	token.mediatagid, ' .
		'	token.tag as tag, ' .
		'	tag.val as `value` ' .
		'from _media_tags as tag ' .
		'left join _media_tag as token on tag.mediatagid = token.mediatagid ' .
		'where tag.mediaid = :mediaid ' .
		'order by token.tag asc', array (
			':mediaid' => $this->_id
		));

		return $this->_tags;
	}

	public function removeTag($tagid) {

		Aitsu_Db :: query('' .
		'delete from _media_tags ' .
		'where ' .
		'	mediaid = :mediaid ' .
		'	and mediatagid = :tagid', array (
			':mediaid' => $this->_id,
			':tagid' => $tagid
		));
	}

	public function setAsMainImage($unset = false) {

		if ($unset) {
			Aitsu_Db :: query('' .
			'update ' .
			'	_art_lang artlang, ' .
			'	_media media ' .
			'set ' .
			'	artlang.mainimage = null ' .
			'where ' .
			'	artlang.idart = media.idart ' .
			'	and artlang.idlang = :idlang ' .
			'	and media.mediaid = :id', array (
				':id' => $this->_id,
				':idlang' => $this->_idlang
			));
			return $this;
		}

		Aitsu_Db :: query('' .
		'update ' .
		'	_art_lang artlang, ' .
		'	_media media ' .
		'set ' .
		'	artlang.mainimage = media.filename ' .
		'where ' .
		'	artlang.idart = media.idart ' .
		'	and artlang.idlang = :idlang ' .
		'	and media.mediaid = :id', array (
			':id' => $this->_id,
			':idlang' => $this->_idlang
		));

		return $this;
	}

}