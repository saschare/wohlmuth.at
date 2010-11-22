<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Role.php 18713 2010-09-13 07:39:04Z akm $}
 */

/*
CREATE TABLE IF NOT EXISTS `con_acl_role` (
  `roleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  PRIMARY KEY (`roleid`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `con_acl_privileges` (
  `roleid` int(10) unsigned NOT NULL,
  `privilegeid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`roleid`,`privilegeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
*/

class Aitsu_Persistence_Role extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
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
		'select * from _acl_role where roleid = :id', array (
			':id' => $this->_id
		));

		$this->_data['privileges'] = Aitsu_Db :: fetchCol('' .
		'select privilegeid from _acl_privileges where roleid = :id', array (
			':id' => $this->_id
		));

		$this->_data['clients'] = Aitsu_Db :: fetchCol('' .
		'select idclient from _acl_clients where roleid = :id', array (
			':id' => $this->_id
		));

		$this->_data['languages'] = Aitsu_Db :: fetchCol('' .
		'select idlang from _acl_languages where roleid = :id', array (
			':id' => $this->_id
		));

		$this->_data['resources'] = Aitsu_Db :: fetchCol('' .
		'select resourceid from _acl_resources where roleid = :id', array (
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

		$this->_id = Aitsu_Db :: put('_acl_role', 'roleid', $this->_data);

		Aitsu_Db :: query('' .
		'delete from _acl_privileges where roleid = :roleid ', array (
			':roleid' => $this->_id
		));
		if (isset ($this->_data['privileges']) && is_array($this->_data['privileges'])) {
			foreach ($this->_data['privileges'] as $privilege) {
				Aitsu_Db :: put('_acl_privileges', null, array (
					'roleid' => $this->_id,
					'privilegeid' => $privilege
				));
			}
		}

		Aitsu_Db :: query('' .
		'delete from _acl_clients where roleid = :roleid ', array (
			':roleid' => $this->_id
		));
		if (isset ($this->_data['clients']) && is_array($this->_data['clients'])) {
			foreach ($this->_data['clients'] as $client) {
				Aitsu_Db :: put('_acl_clients', null, array (
					'roleid' => $this->_id,
					'idclient' => $client
				));
			}
		}

		Aitsu_Db :: query('' .
		'delete from _acl_languages where roleid = :roleid ', array (
			':roleid' => $this->_id
		));
		if (isset ($this->_data['languages']) && is_array($this->_data['languages'])) {
			foreach ($this->_data['languages'] as $lang) {
				Aitsu_Db :: put('_acl_languages', null, array (
					'roleid' => $this->_id,
					'idlang' => $lang
				));
			}
		}

		Aitsu_Db :: query('' .
		'delete from _acl_resources where roleid = :roleid ', array (
			':roleid' => $this->_id
		));
		if (isset ($this->_data['resources']) && is_array($this->_data['resources'])) {
			foreach ($this->_data['resources'] as $resource) {
				Aitsu_Db :: put('_acl_resources', null, array (
					'roleid' => $this->_id,
					'resourceid' => $resource
				));
			}
		}
	}

	public function remove() {

		Aitsu_Db :: query('delete from _acl_role where roleid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getAll() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _acl_role ' .
		'order by identifier asc ');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['roleid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getAsArray() {

		$results = Aitsu_Db :: fetchAll('' .
		'select * from _acl_role ' .
		'order by ' .
		'	identifier asc ');

		if (!$results) {
			return array ();
		}

		$return = array ();
		foreach ($results as $row) {
			$return[$row['roleid']] = $row['identifier'];
		}

		return $return;
	}

	public static function getFullRoles() {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	privs.roleid, ' .
		'	priv.identifier, ' .
		'	priv.privilegeid ' .
		'from _acl_privileges as privs ' .
		'left join _acl_privilege as priv on privs.privilegeid = priv.privilegeid');

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$privs[$result['roleid']][] = array (
				'privilegeid' => $result['privilegeid'],
				'identifier' => $result['identifier']
			);
		}

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	clients.roleid, ' .
		'	client.idclient, ' .
		'	client.name ' .
		'from _acl_clients as clients ' .
		'left join _clients as client on clients.idclient = client.idclient');

		foreach ($results as $result) {
			$clients[$result['roleid']][] = array (
				'idclient' => $result['idclient'],
				'name' => $result['name']
			);
		}

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	langs.roleid, ' .
		'	lang.idlang, ' .
		'	lang.name ' .
		'from _acl_languages as langs ' .
		'left join _lang as lang on langs.idlang = lang.idlang');

		foreach ($results as $result) {
			$langs[$result['roleid']][] = array (
				'idlang' => $result['idlang'],
				'name' => $result['name']
			);
		}

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	resources.roleid, ' .
		'	res.resourceid, ' .
		'	res.name ' .
		'from _acl_resources as resources ' .
		'left join _acl_resource as res on resources.resourceid = res.resourceid');

		foreach ($results as $result) {
			$res[$result['roleid']][] = array (
				'resourceid' => $result['resourceid'],
				'name' => $result['name']
			);
		}

		$roles = self :: getAll();

		foreach ($roles as $role) {
			if (isset ($privs[$role->roleid])) {
				$role->privileges = $privs[$role->roleid];
			}
			if (isset ($clients[$role->roleid])) {
				$role->clients = $clients[$role->roleid];
			}
			if (isset ($langs[$role->roleid])) {
				$role->languages = $langs[$role->roleid];
			}
			if (isset ($res[$role->roleid])) {
				$role->resources = $res[$role->roleid];
			}
			$return[] = $role;
		}

		return $return;
	}
}