<?php


/**
 * Usermanagement role.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Role.php 16337 2010-05-07 17:18:41Z akm $}
 */

class Aitsu_Usermanagement_Role {

	protected $id;
	protected $name;
	protected $rights = array ();

	protected function __construct($id) {

		if ($id != null) {
			$this->id = $id;
			$this->_readFromDb();
		}
	}

	public static function factory($id) {

		$instance = new self($id);
		return $instance;
	}

	public static function getRoles($pattern = '*') {

		$pattern = str_replace('*', '%', $pattern);

		return Aitsu_Db :: fetchAll('' .
		'select * from _um_role ' .
		'where name like ? ' .
		'order by name asc ');
	}

	public static function addNew($name) {

		$role = self :: factory();
		$role->name = $name;
		$role->_save();

		return $role;
	}

	protected function _save() {

		$this->id = Aitsu_Db :: query('' .
		'insert into _um_role ' .
		'(name) ' .
		'values ' .
		'(?) ', array (
			$this->name
		)) . getLastInsertId();
	}

	protected function _readFromDb() {

		$rights = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	ro.name, ' .
		'	ri.rightid ' .
		'from _um_role as ro ' .
		'left join _um_role_right as rori on ro.roleid = rori.roleid ' .
		'left join _um_right as ri on rori.rightid = ri.rightid ' .
		'where ro.roleid = ? ', array (
			$this->id
		));

		if (!$rights) {
			return;
		}

		foreach ($rights as $right) {
			$this->name = $right['name'];
			$this->rights[] = $right['rightid'];
		}
	}

	public function setRights($rights) {

		$this->rights = $rights;

		Aitsu_Db :: query('' .
		'delete from _um_role_right ' .
		'where roleid = ? ', array (
			$this->id
		));

		if ($rights == null) {
			return;
		}

		foreach ($rights as $right) {
			Aitsu_Db :: query('' .
			'insert into _um_role_right ' .
			'(roleid, rightid) ' .
			'values ' .
			'(?, ?) ', array (
				$this->id,
				$right
			));
		}

		return $this;
	}

	public static function getAvailableRights() {

		$return = Aitsu_Db :: fetchAll('' .
		'select * from _um_right order by name asc ');

		return $return;
	}
}