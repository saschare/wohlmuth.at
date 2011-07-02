<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Ee_Rewrite_History {

	protected $url;

	protected function __construct() {

		$this->url = strtok($_SERVER['REQUEST_URI'], '?');

		$parameters = Aitsu_Config :: get('rewrite.history.params');

		if (is_object($parameters)) {
			$query = null;

			foreach ($parameters as $parameter) {
				if (!empty ($_GET[$parameter])) {
					$query[$parameter] = $_GET[$parameter];
				}
			}

			if (is_array($query)) {
				$this->url = $this->url . '?' . http_build_query($query);
			}
		}
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

			$count = Aitsu_Db :: fetchOneC('eternal', '' .
			'select count(*) from _aitsu_rewrite_history ' .
			'where ' .
			'	url = :url ' .
			'	and idartlang = :idartlang ' .
			'', array (
				':url' => $this->url,
				':idartlang' => $idartlang
			));

			if ($count > 0) {
				return;
			}

			Aitsu_Db :: query('' .
			'insert into _aitsu_rewrite_history ' .
			'(url, idartlang, created) ' .
			'values ' .
			'(:url, :idartlang, now())' .
			'', array (
				'url' => $this->url,
				':idartlang' => $idartlang
			));
		} catch (Exception $e) {
			return;
		}
	}

	public function getAlternative() {

		$url = Aitsu_Db :: fetchOne('' .
		'select ' .
		'	concat(\'/\', catlang.url, \'/\', artlang.urlname, \'.html\') as target ' .
		'from _aitsu_rewrite_history as history ' .
		'left join _art_lang as artlang on artlang.idartlang = history.idartlang ' .
		'left join _cat_art as catart on catart.idart = artlang.idart ' .
		'left join _cat_lang as catlang on (catlang.idcat = catart.idcat and catlang.idlang = artlang.idlang) ' .
		'where ' .
		'	history.url = :url ' .
		'	and artlang.online = 1 ' .
		'order by ' .
		'	history.created desc ' .
		'limit 0, 1 ' .
		'', array (
			':url' => $this->url
		));

		if ($url) {
			return $url;
		}

		return false;
	}

}