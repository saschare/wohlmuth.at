<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: User.php 18768 2010-09-14 18:33:34Z akm $}
 */

class Aitsu_Persistence_View_User {

	public static function auth($login, $password, $hashed = false) {

		if (!$hashed) {
			$password = md5($password);
		}

		$userid = Aitsu_Db :: fetchOne('' .
		'select userid from _acl_user ' .
		'where ' .
		'	login = :login ' .
		'	and password = :password', array (
			':login' => $login,
			':password' => $password
		));

		return self :: _getUserData($userid);
	}

	protected static function _getUserData($userid) {

		if (!is_numeric($userid)) {
			return false;
		}

		$return = array ();

		$return['id'] = $userid;

		return (object) $return;
	}

	public static function privileges($id) {

		return Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	privileg.identifier as privileg, ' .
		'	client.idclient as idclient, ' .
		'	language.idlang, ' .
		'	resource.resourcetype as resourcetype, ' .
		'	resource.identifier as resourceid, ' .
		'	cat.lft as resourceleft, ' .
		'	cat.rgt as resourceright ' .
		'from _acl_roles as roles ' .
		'left join _acl_privileges as privileges on roles.roleid = privileges.roleid ' .
		'left join _acl_privilege as privileg on privileges.privilegeid = privileg.privilegeid ' .
		'left join _acl_clients as client on roles.roleid = client.roleid ' .
		'left join _acl_languages as language on roles.roleid = language.roleid ' .
		'left join _acl_resources as res on roles.roleid = res.roleid ' .
		'left join _acl_resource as resource on res.resourceid = resource.resourceid ' .
		'left join _cat as cat on resource.resourcetype = \'cat\' and cat.idcat = resource.identifier ' .
		'where ' .
		'	roles.userid = :id ' .
		'order by ' .
		'	privileg asc, ' .
		'	idclient asc, ' .
		'	idlang asc, ' .
		'	resourcetype asc, ' .
		'	resourceid asc', array (
			':id' => $id
		));
	}
}