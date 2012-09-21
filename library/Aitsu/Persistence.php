<?php


/**
 * Generic persistence class (some kind of server session).
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Persistence.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Persistence {

	protected $data = null;
	protected $db;
	protected $namespace = 'default';
	protected $token;
	protected $persisted = false;

	protected function __construct() {
		$this->db = Aitsu_Registry :: get()->db;
		$this->token = uniqid();
	}

	public static function getInstance($namespace = null, $token = null) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		if ($namespace != null && $token != null) {
			$instance->namespace = $namespace;
			$instance->token = $token;
			$instance->data = null;
		}

		return $instance;
	}

	protected function _deserializeData() {

		if ($this->data != null) {
			return;
		}

		$result = Aitsu_Db :: fetchOne("" .
		"select data from _aitsu_generic_persistence " .
		"where " .
		"	namespace = ? " .
		"	and token = ? " .
		"	and ( " .
		"		expiration is null " .
		"		or expiration > now() " .
		"		) " .
		"", array (
			$this->namespace,
			$this->token
		));
		
		if (!$result) {
			$this->data = null;
			return;
		}
		
		$this->data = @unserialize($result); 
	}

	public function setNamespace($namespace) {

		$this->namespace = $namespace;

		return $this;
	}

	public function setToken($token) {

		$this->token = $token;

		return $this;
	}

	public function __get($key) {

		if ($this->data == null) {
			$this->_deserializeData();
		}

		if (isset ($this->data[$key])) {
			return $this->data[$key];
		}

		return null;
	}

	public function __set($key, $value) {

		if ($this->data == null) {
			$this->_deserializeData();
		}

		$this->data[$key] = $value;

		$this->persisted = false;

		return $this;
	}

	public function save($seconds) {

		if ($this->persisted) {
			return;
		}

		$this->persisted = true;

		Aitsu_Db :: query("" .
		"delete from _aitsu_generic_persistence where expiration is not null and expiration < now()");

		if ($seconds == 0) {
			Aitsu_Db :: query("" .
			"replace into _aitsu_generic_persistence " .
			"(namespace, token, data, created, expiration) " .
			"values " .
			"(?, ?, ?, now(), null) " .
			"", array (
				$this->namespace,
				$this->token,
				serialize($this->data)
			));
			return;
		}

		Aitsu_Db :: query("" .
		"replace into _aitsu_generic_persistence " .
		"(namespace, token, data, created, expiration) " .
		"values " .
		"(?, ?, ?, now(), date_add(now(), interval {$seconds} second)) " .
		"", array (
			$this->namespace,
			$this->token,
			@serialize($this->data)
		));
	}

}