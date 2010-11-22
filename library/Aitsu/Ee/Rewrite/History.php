<?php


/**
 * Url rewrite history.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: History.php 18147 2010-08-16 15:55:44Z akm $}
 */

class Aitsu_Ee_Rewrite_History {

	protected $url;

	protected function __construct() {

		$this->url = $_SERVER['REQUEST_URI'];
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function saveUrl() {

		try {
			$idartlang = Aitsu_Registry :: get()->env->idartlang;
			
			if (Aitsu_Registry :: get()->env->idart == Aitsu_Registry :: get()->config->sys->errorpage) {
				return;
			}

			$count = Aitsu_Db :: fetchOne("" .
			"select count(*) from _aitsu_rewrite_history " .
			"where " .
			"	url = ? " .
			"	and idartlang = ? " .
			"", array (
				$this->url,
				$idartlang
			));
	
			if ($count > 0) {
				return;
			}
	
			Aitsu_Db :: query("" .
			"replace into _aitsu_rewrite_history " .
			"(url, idartlang, created) " .
			"values " .
			"(?, ?, now())" .
			"", array (
				$this->url,
				$idartlang
			));
		} catch (Exception $e) {
			return;
		}
	}

	public function getAlternative() {

		$url = Aitsu_Db :: fetchOne("" .
		"select " .
		"	current.url " .
		"from _aitsu_rewrite_history as history " .
		"left join _art_lang as artlang on history.idartlang = artlang.idartlang " .
		"left join _aitsu_rewrite_history as current on artlang.idartlang = current.idartlang " .
		"where " .
		"	history.url = ? " .
		"	and artlang.online = 1 " .
		"order by " .
		"	current.created desc " .
		"limit 0, 1 " .
		"", array (
			$this->url
		));
		
		if ($url) {
			return $url;
		}
		
		return false;
	}
}