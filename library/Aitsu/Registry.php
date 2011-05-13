<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Aitsu_Registry {

	protected $_registry = array ();

	protected function __construct() {

		$this->_registry['expiryTime'] = empty (Aitsu_Registry :: get()->config->cache->browser->expireTime) ? 0 : Aitsu_Registry :: get()->config->cache->browser->expireTime;
	}

	protected static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function get() {
		return self :: getInstance();
	}

	public function __get($name) {

		if (!array_key_exists($name, $this->_registry) || $this->_registry[$name] === null) {
			$this->_registry[$name] = Aitsu_Registry_Entry :: factory();
		}

		return $this->_registry[$name];
	}

	public function __isset($name) {

		if (!array_key_exists($name, $this->_registry) || $this->_registry[$name] === null) {
			return false;
		}

		return true;
	}

	public function __set($name, $value) {

		$this->_registry[$name] = $value;
	}

	public static function isFront($set = null) {

		if ($set != null) {
			Aitsu_Registry :: get()->env->front = (boolean) $set;
		}

		return isset (Aitsu_Registry :: get()->env->front) && Aitsu_Registry :: get()->env->front == true;
	}

	public static function isEdit($set = null) {

		if ($set != null) {
			Aitsu_Registry :: get()->env->edit = (boolean) $set;
		}

		if (Aitsu_Registry :: isFront()) {
			/*
			 * We are in frontend mode and therefore edit mode must
			 * be false.
			 */
			return false;
		}

		$user = Aitsu_Adm_User :: getInstance();
		if ($user == null) {
			return false;
		}

		return isset (Aitsu_Registry :: get()->env->edit) && Aitsu_Registry :: get()->env->edit == true;
	}

	public static function isBoxModel($set = null) {

		if ($set != null) {
			Aitsu_Registry :: get()->env->boxModel = (boolean) $set;
		}

		return isset (Aitsu_Registry :: get()->env->boxModel) && Aitsu_Registry :: get()->env->boxModel == true;
	}

	public static function translator($adapter = null) {

		if ($adapter != null) {
			Aitsu_Registry :: get()->Zend_Translate = $adapter;
		}

		return Aitsu_Registry :: get()->Zend_Translate;
	}

	public static function setExpireTime($time) {

		/*$reg = self :: getInstance();

		$reg->_registry['expireTime'] = min(array (
			$reg->_registry['expireTime'],
			$time
		));*/
	}

	public static function getExpireTime() {
		return 0;
		/*$reg = self :: getInstance();
		
		return $reg->_registry['expireTime'];*/
	}

}