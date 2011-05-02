<?php


/**
 * aitsu EE module cache
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Modul.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Ee_Cache_Modul {

	private $idart;
	private $idlang;
	private $db;
	private $requestHash;
	private $cache = array ();
	private $time;
	private $useSession = false;

	/**
	 * Constructor.
	 * @param Integer Idart of the current article.
	 * @param Integer Primary key of the current language.
	 */
	private function __construct($idart, $idlang, $useSession) {

		$this->idart = $idart;
		$this->idlang = $idlang;
		$this->useSession = $useSession;

		$this->requestHash = $this->getRequestHash();

		$this->fetchCache();
	}

	/**
	 * Returns a hash of the request based on sorted and serialized
	 * values of the get and post data.
	 * @return String MD5 hash of the request.
	 */
	private function getRequestHash() {

		$get = $_GET;
		$post = $_POST;

		ksort($get);
		ksort($post);

		if ($this->useSession) {
			$session = $_SESSION;
			$cookies = $_COOKIE;
			ksort($session);
			ksort($cookies);

			return md5(serialize(array (
				$get,
				$post,
				$session,
				$cookies
			)));
		}

		return md5(serialize(array (
			$get,
			$post
		)));
	}

	/**
	 * Fetches the cached data of the current article.
	 * @return void 
	 */
	private function fetchCache() {

		$count = Aitsu_Db :: fetchOne("" .
		"select count(cache.idart) " .
		"from " .
		"	_modul_cache as cache, " .
		"	_art_lang as art " .
		"where art.lastmodified > cache.created" .
		"");
		if ($count > 0) {
			Aitsu_Db :: query("" .
			"truncate _modul_cache" .
			"");
		}

		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	modulid, " .
		"	output, " .
		"	if(expiration > now(), 0, 1) as expired " .
		"from _modul_cache " .
		"where " .
		"	idart = ? " .
		"	and idlang = ? " .
		"	and request = ? " .
		"", array (
			$this->idart,
			$this->idlang,
			$this->requestHash
		));

		if (empty ($results)) {
			return;
		}

		foreach ($results as $result) {
			$this->cache[$result['modulid']]['expired'] = $result['expired'];
			$this->cache[$result['modulid']]['output'] = $result['output'];
		}

	}

	/**
	 * Singleton.
	 * @param Integer Idart of the current article.
	 * @param Integer Primary key of the current language.
	 */
	public static function getInstance($useSession = null, $idart = null, $idlang = null) {

		static $instance;
		static $last;

		if ($useSession == null) {
			$useSession = $last;
		} else {
			$last = $useSession;
		}

		if ($useSession == null) {
			$useSession = false;
		}

		if (!isset ($instance[$useSession])) {
			$instance[$useSession] = new self($idart, $idlang, $useSession);
		}

		return $instance[$useSession];
	}

	/**
	 * Starts the caching mechanism.
	 * @param Integer Modul identifier (usually the currentContainer variable).
	 * @param String Caching period. E.g. 12 second or 1 week or 2 month or 1 year.
	 */
	public static function start($modulId, $period = null, $useSession = false) {

		$idart = Aitsu_Registry :: get()->env->idart;
		$idlang = Aitsu_Registry :: get()->env->idlang;

		$instance = self :: getInstance($useSession, $idart, $idlang);

		$instance->modulId = $modulId;
		$instance->period = $period;

		$instance->time['start'] = microtime(true);
		
		$instance->_clearCache();

		if ($period == null || Aitsu_Registry :: get()->config->cache->modul->enable != true) {
			$instance->modulId = null;
			return true;
		}

		if (array_key_exists($modulId, $instance->cache) && $instance->cache[$modulId]['expired'] == 0) {
			return false;
		}

		ob_start();
		
		return true;
	}

	/**
	 * Returns the cached content.
	 * @return String Output.
	 */
	public static function out() {

		$instance = self :: getInstance();

		$instance->time['end'] = microtime(true);

		if ($instance->modulId == null) {
			return '';
		}

		if (array_key_exists($instance->modulId, $instance->cache) && $instance->cache[$instance->modulId]['expired'] == 0) {
			return $instance->cache[$instance->modulId]['output'];
		}

		$out = ob_get_contents();
		ob_end_clean();

		if (array_key_exists($instance->modulId, $instance->cache)) {
			Aitsu_Db :: query("" .
			"delete from _modul_cache " .
			"where " .
			"	idart = ? " .
			"	and idlang = ? " .
			"	and modulid = ? " .
			"	and request = ? " .
			"", array (
				$instance->idart,
				$instance->idlang,
				$instance->modulId,
				$instance->requestHash
			));
		}
		Aitsu_Db :: query('' .
		'insert into _modul_cache ' .
		'(idart, idlang, modulid, request, output, created, expiration) ' .
		'values ' .
		'(?, ?, ?, ?, ?, now(), date_add(now(), interval ' . $instance->period . '))', array (
			$instance->idart,
			$instance->idlang,
			$instance->modulId,
			$instance->requestHash,
			$out
		));

		return $out;
	}
	
	protected function _clearCache() {
		
		if (!isset(Aitsu_Registry :: get()->config->cache->clear->key) || !isset($_GET[Aitsu_Registry :: get()->config->cache->clear->key])) {
			return;
		}
		
		Aitsu_Db :: query('truncate _modul_cache');
	}
}