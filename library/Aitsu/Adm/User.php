<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Adm_User {

	static $_instance = null;
	protected $_id;
	protected $_data;
	protected $_privs = array (); // privileges

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
		$this->_loadRoles();
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

	protected function _loadRoles() {

		$privileges = Aitsu_Persistence_View_User :: privileges($this->_id);

		if (!$privileges) {
			return;
		}

		foreach ($privileges as $priv) {
			$privileg = (object) array (
				'client' => $priv['idclient'],
				'language' => $priv['idlang'],
				'area' => trim(strtok($priv['privileg'], ':')),
				'action' => trim(strtok("\n")),
				'resource' => (object) array (
					'type' => $priv['resourcetype'],
					'id' => $priv['resourceid'],
					'left' => $priv['resourceleft'],
					'right' => $priv['resourceright']
				)
			);
			$this->_privs['client'][$privileg->client]['language'][$privileg->language][$privileg->area][$privileg->action][] = $privileg;
			$this->_privs['language'][$privileg->language][$privileg->area][$privileg->action][] = $privileg;
			$this->_privs['area'][$privileg->area][$privileg->action][] = $privileg;
		}
	}

	public function isAllowed(array $res) {

		if (isset ($res['client']) && !isset ($this->_privs['client'][$res['client']])) {
			return false;
		}

		if (isset ($res['language']) && !isset ($this->_privs['language'][$res['language']])) {
			return false;
		}

		if (!isset ($res['area']) && !isset ($res['resource'])) {
			/*
			 * No further tests necessary.
			 */
			return true;
		}

		if (isset ($res['area']) && !isset ($this->_privs['area'][$res['area']])) {
			return false;
		}

		if (!isset ($res['action'])) {
			/*
			 * No further tests necessary.
			 */
			return true;
		}

		if (isset ($res['language'])) {
			$privileges = $this->_privs['language'][$res['language']];
		} else {
			$privileges = $this->_privs['area'];
		}

		if (empty ($privileges)) {
			/*
			 * The current user has no specific rights.
			 */
			return false;
		}

		foreach ($privileges as $privs) {
			if (isset ($privs[$res['action']])) {
				foreach ($privs[$res['action']] as $priv) {
					if ($this->_hasAccess($res, $priv)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	protected function _hasAccess($res, $privilege) {

		if ($privilege == null) {
			return false;
		}

		if (isset ($res['client']) && (!isset ($privilege->client) || $privilege->client != $res['client'])) {
			return false;
		}

		if (isset ($res['language']) && (!isset ($privilege->language) || $privilege->language != $res['language'])) {
			return false;
		}

		if (isset ($res['area']) && (!isset ($privilege->area) || $privilege->area != $res['area'])) {
			return false;
		}

		if (isset ($res['action']) && (!isset ($privilege->action) || $privilege->action != $res['action'])) {
			return false;
		}

		if (isset ($res['resource'])) {
			if (!isset ($privilege->resource)) {
				return false;
			}
			if ($res['resource']['type'] != $privilege->resource->type) {
				return false;
			}
			if ($res['resource']['id'] == $privilege->resource->id) {
				return true;
			}
			if ($res['resource']['type'] == 'cat') {
				if ($privilege->resource->id == 0) {
					return true;
				}
				if (!isset ($res['resource']['left'])) {
					/*
					 * No lft value given. This has to be determined by a
					 * db access.
					 */
					$res['resource']['lft'] = Aitsu_Db :: fetchOne('' .
					'select lft from _cat where idcat = :idcat', array (
						':idcat' => $res['resource']['id']
					));
				}
				if ($res['resource']['lft'] < $privilege->resource->left || $res['resource']['lft'] > $privilege->resource->right) {
					return false;
				}
			}
		}

		return true;
	}

}