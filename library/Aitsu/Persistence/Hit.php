<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Hit.php 19564 2010-10-25 13:16:25Z akm $}
 */

class Aitsu_Persistence_Hit extends Aitsu_Persistence_Abstract {

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

		return $this;
	}

	public function remove() {

		return $this;
	}

	public function getData() {

		return $this->_data;
	}

	public static function hit() {

		if (Aitsu_Registry :: isEdit()) {
			return;
		}

		$idartlang = Aitsu_Registry :: get()->env->idartlang;

		Aitsu_Db :: query('' .
		'insert into _hit (idartlang, d, h) ' .
		'values (:idartlang, :date, :hour) ' .
		'on duplicate key update hits = hits + 1', array (
			':idartlang' => $idartlang,
			':date' => date('Y-m-d'),
			':hour' => date('H')
		));
	}

	public static function getMostImpressedByBranch($idcat = null, $interval = 30, $limit = 100) {

		$idcat = is_null($idcat) ? Aitsu_Registry :: get()->env->idcat : $idcat;
		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idart = Aitsu_Registry :: get()->env->idart;

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idartlang, ' .
		'	artlang.idart, ' .
		'	artlang.title, ' .
		'	artlang.pagetitle, ' .
		'	artlang.summary, ' .
		'	catlang.idcat, ' .
		'	catlang.idcatlang, ' .
		'	catlang.name as catname, ' .
		'	if (artlang.idartlang = catlang.startidartlang, 1, 0) as isstart ' .
		'from _cat as parent ' .
		'left join _cat as child on child.lft between parent.lft and parent.rgt and parent.idclient = child.idclient ' .
		'left join _cat_art as catart on child.idcat = catart.idcat ' .
		'left join _art_lang as artlang on catart.idart = artlang.idart ' .
		'left join _cat_lang as catlang on child.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
		'left join _hit as hit on artlang.idartlang = hit.idartlang ' .
		'where ' .
		'	hit.d >= date_sub(now(), interval ' . $interval . ' day) ' .
		'	and artlang.online = 1 ' .
		'	and (artlang.pubfrom is null or artlang.pubfrom < now()) ' .
		'	and (artlang.pubuntil is null or artlang.pubuntil + 1 > now())' .
		'	and parent.idcat = :idcat ' .
		'	and artlang.idlang = :idlang ' .
		'	and artlang.idart != :idart ' .
		'	' .
		'group by ' .
		'	artlang.idartlang, ' .
		'	catlang.idcatlang ' .
		'order by ' .
		'	sum(hit.hits) desc ' .
		'limit 0, ' . $limit, array (
			':idcat' => $idcat,
			':idlang' => $idlang,
			':idart' => $idart
		));
	}
}