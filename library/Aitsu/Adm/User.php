<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_User {

	static $_instance = null;
	protected $_id;
	protected $_data;
	protected $_allowedRes = array ();

	protected function __construct($id) {

		$this->_id = $id;

		if ($id == 'setup') {
			$this->_privs['area']['script']['execute'][] = (object) array (
				'client' => null,
				'language' => null,
				'area' => 'script',
				'action' => 'execute',
				'resource' => null
			);
			return;
		}

		$this->_data = Aitsu_Persistence_User :: factory($id)->load()->getData();
	}

	public static function getInstance() {

		return self :: $_instance;
	}

	public static function setupLogin() {

		self :: $_instance = new self('setup');

		return self :: $_instance;
	}

	public static function login($login, $password, $hashed = false) {

		$user = Aitsu_Persistence_View_User :: auth($login, $password, $hashed);
		if ($user !== false) {
			self :: $_instance = new self($user->id);
			return true;
		}

		return false;
	}

	public function __get($key) {

		if (isset ($this->_data[$key])) {
			return $this->_data[$key];
		}

		return null;
	}

	public static function rehydrate($instance) {

		self :: $_instance = $instance;
	}

	public function getProperty($key) {

		return Aitsu_Persistence_User :: factory($this->_id)->load()->getProperty($key);
	}

	public function getId() {

		return $this->_id;
	}

	public function isAllowed(array $res) {

		if ($this->_id == 'setup') {
			return false;
		}

		/*
		 * First, we build a hash representing the resource to enable
		 * temporary persistence.
		 */
		$index = hash('md4', var_export($res, true));

		/*
		 * Return the result if already available.
		 */
		if (isset ($this->_allowedRes[$index])) {
			return $this->_allowedRes[$index];
		}

		/*
		 * Then we gather the details for the where clause and the
		 * data to be bound to the statement.
		 */
		$clause = array (
			'roles.userid = :id'
		);
		$data = array (
			':id' => $this->_id
		);
		if (isset ($res['client'])) {
			$clause[] = "client.idclient = :client";
			$data[':client'] = $res['client'];
		}
		if (isset ($res['language'])) {
			$clause[] = "language.idlang = :language";
			$data[':language'] = $res['language'];
		}
		if (isset ($res['area'])) {
			$clause[] = "trim(substring_index(privileg.identifier, ':', 1)) = :area";
			$data[':area'] = $res['area'];
		}
		if (isset ($res['action'])) {
			$clause[] = "trim(substring_index(privileg.identifier, ':', -1)) = :action";
			$data[':action'] = $res['action'];
		}
		if (isset ($res['resource'])) {
			if ($res['resource']['type'] == 'cat') {
				$clause[] = "(cat.idcat = :idcat or resource.resourceid = 1)";
				$data[':idcat'] = $res['resource']['id'];
			}
			elseif ($res['resource']['type'] == 'art') {
				$clause[] = "art.idart = :idart";
				$data[':idart'] = $res['resource']['id'];
			}
		}

		/*
		 * Query the database layer for the count of matches.
		 */
		$allowed = Aitsu_Db :: fetchOne('' .
		'select count(*) ' .
		'from _acl_roles as roles ' .
		'left join _acl_privileges as privileges on roles.roleid = privileges.roleid ' .
		'left join _acl_privilege as privileg on privileges.privilegeid = privileg.privilegeid ' .
		'left join _acl_clients as client on roles.roleid = client.roleid ' .
		'left join _acl_languages as language on roles.roleid = language.roleid ' .
		'left join _acl_resources as res on roles.roleid = res.roleid ' .
		'left join _acl_resource as resource on res.resourceid = resource.resourceid ' .
		'left join _cat as catparent on resource.resourcetype = \'cat\' and catparent.idcat = resource.identifier ' .
		'left join _cat as cat on cat.lft between catparent.lft and catparent.rgt ' .
		'left join _art as art on resource.resourcetype = \'art\' and art.idart = resource.identifier ' .
		'where ' . implode(' and ', $clause), $data);

		/*
		 * Hold the result for further use during the same request.
		 */
		$this->_allowedRes[$index] = (boolean) $allowed;

		return (boolean) $allowed;
	}

}