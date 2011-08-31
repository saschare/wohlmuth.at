<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3conceps AG
 */

class Aitsu_Application_Status {

	protected $_preview = false;
	protected $_environment = null;
	protected $_edit = false;
	protected $_locked = false;
	protected $_structured = false;
	protected $_channel = null;

	static protected $_instance = null;

	protected function __construct() {
	}

	protected static function _getInstance() {

		if (empty (self :: $_instance)) {
			self :: $_instance = new self();
		}

		return self :: $_instance;
	}

	public static function isStructured($set = null) {

		$instance = self :: _getInstance();

		if (!is_null($set)) {
			$instance->_structured = (boolean) $set;
		}

		return $instance->_structured;
	}

	public static function isPreview($set = null) {

		$instance = self :: _getInstance();

		if ($instance->_locked && !is_null($set)) {
			throw new Exception('Preview mode may only be set within the bootstrap.');
		}

		if (!is_null($set)) {
			$instance->_preview = (boolean) $set;
		}

		if (is_null($instance->_environment)) {
			/*
			 * Preview always returns true, if no environment is set.
			 */
			return true;
		}

		if ($instance->_edit) {
			/*
			 * Preview always returns true, if edit is set to true.
			 */
			return true;
		}

		if (!isset (Aitsu_Registry :: get()->config->sys->usePublishing) || Aitsu_Registry :: get()->config->sys->usePublishing == false) {
			return true;
		}

		return $instance->_preview;
	}

	public static function isEdit($set = null) {

		$instance = self :: _getInstance();

		if ($instance->_locked && !is_null($set)) {
			throw new Exception('Edit mode may only be set within the bootstrap.');
		}

		if (!is_null($set)) {
			$instance->_edit = (boolean) $set;
		}

		$user = Aitsu_Adm_User :: getInstance();
		if (is_null($user)) {
			/*
			 * Edit mode is impossible, if the user is not logged in.
			 */
			return false;
		}

		return $instance->_edit;
	}

	public static function getChannel() {

		$instance = self :: _getInstance();
		return $instance->_channel;
	}

	public static function setChannel($channel) {

		$instance = self :: _getInstance();
		$instance->_channel = $channel;
	}

	public static function setEnv($env) {

		$instance = self :: _getInstance();

		if ($instance->_locked) {
			throw new Exception('Environment may only be set within the bootstrap.');
		}

		$instance->_environment = $env;
	}

	public static function getEnv() {

		return self :: _getInstance()->_environment;
	}

	public static function isAllowCaching($loginAllowed = false) {

		$instance = self :: _getInstance();

		if ($instance->_edit) {
			/*
			 * Return false if edit mode is on.
			 */
			return false;
		}

		if ($instance->_preview && (!isset (Aitsu_Registry :: get()->config->sys->usePublishing) || Aitsu_Registry :: get()->config->sys->usePublishing == true)) {
			/*
			 * Return false if preview mode is on, except publishing
			 * is disabled.
			 */
			return false;
		}

		$user = Aitsu_Adm_User :: getInstance();
		if (!$loginAllowed && !is_null($user)) {
			/*
			 * Return false, if user is logged in.
			 */
			return false;
		}

		if (isset (Aitsu_Registry :: get()->config->cache->internal->enable) && !Aitsu_Registry :: get()->config->cache->internal->enable) {
			/*
			 * Return false, if internal cache is disabled.
			 */
			return false;
		}

		return true;
	}

	public static function lock() {

		self :: _getInstance()->_locked = true;
	}

	public static function version() {

		$version = '$version/2.3.7/revision/28$';
		
		$version = str_replace(array (
			'version/',
			'/revision/',
			'$'
		), array (
			'',
			'-',
			''
		), $version);
		
		return $version;
	}

}