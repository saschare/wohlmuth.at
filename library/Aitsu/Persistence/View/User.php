<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_View_User {

	public static function auth($login, $password, $hashed = false) {

		$userid = null;

		if ($hashed) {
			/*
			 * Password hash has been used to authenticate
			 * instead of the plain text password.
			 */
			$userid = Aitsu_Db :: fetchOne('' .
			'select userid from _acl_user ' .
			'where ' .
			'	login = :login ' .
			'	and password = :password ' .
			'	and (' .
			'		acfrom is null ' .
			'		or acfrom < now() ' .
			'	) ' .
			'	and (' .
			'		acuntil is null ' .
			'		or acuntil > now() ' .
			'	)', array (
				':login' => $login,
				':password' => $password
			));
		} else {
			/*
			 * Password has been given as plain text.
			 */

			$user = Aitsu_Db :: fetchRow('' .
			'select userid, password from _acl_user ' .
			'where ' .
			'	login = :login ' .
			'	and (' .
			'		acfrom is null ' .
			'		or acfrom < now() ' .
			'	) ' .
			'	and (' .
			'		acuntil is null ' .
			'		or acuntil > now() ' .
			'	) ', array (
				':login' => $login
			));

			$hasher = new Openwall_PasswordHash(8, FALSE);
			if ($user) {
				if ($hasher->checkPassword($password, $user['password'])) {
					$userid = $user['userid'];
				} elseif (md5($password) == $user['password']) {
					/*
					 * For backward compatiblity reasons, we have to check
					 * whether or not the hash could be a primitive md5 one
					 * and to accept this result as a valid result.
					 */
					$userid = $user['userid'];
				}
			}
		}

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