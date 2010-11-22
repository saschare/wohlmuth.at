<?php


/**
 * Article search and indexing.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Search.php 15508 2010-03-22 20:01:51Z akm $}
 */

class Aitsu_Core_Article_Search {

	private $idlang;
	private $idartlang;
	private $uri;
	private $db;
	private $categories;
	private $mask;
	private $editMode;

	private function __construct($idartlang, $editMode) {

		$this->idartlang = $idartlang;
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->editMode = $editMode;
	}

	public static function getInstance($editMode = false) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self(Aitsu_Registry :: get()->env->idartlang, $editMode);
		}

		return $instance;
	}

	public function resetIndex($content) {

		if ($this->editMode) {
			return;
		}

		try {
			if (Aitsu_Db :: fetchOne("" .
				"select count(search.idartlang) " .
				"from _aitsu_search as search " .
				"left join _art_lang as artlang on search.idartlang = artlang.idartlang " .
				"where " .
				"	( " .
				"		date_add(lastindexed, interval 1 day) > now() " .
				"		or search.lastindexed < artlang.lastmodified " .
				"	)" .
				"	and search.idartlang = ? " .
				"	and search.uri = ? " .
				"", array (
					$this->idartlang,
					$this->uri
				)) == 0) {

				if (preg_match('/<!--\\s*indexFromHere\\s*-->(.*?)<!--\\s*indexUntilHere\\s*-->/s', $content, $match)) {
					$text = $match[1];
				} else {
					$text = $content;
				}

				Aitsu_Db :: query("" .
				"replace into _aitsu_search " .
				"(idartlang, uri, lastindexed, content) " .
				"values " .
				"(?, ?, now(), ?)" .
				"", array (
					$this->idartlang,
					$this->uri,
					html_entity_decode(strip_tags(preg_replace('|<script.*?</script>|s', "", $text)))
				));
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			try {
				Aitsu_Db :: query("" .
				"CREATE TABLE IF NOT EXISTS _aitsu_search (" .
				"idartlang int(10) unsigned NOT NULL," .
				"uri varchar(255) NOT NULL," .
				"lastindexed datetime NOT NULL," .
				"content text NOT NULL," .
				"PRIMARY KEY  (idartlang,uri)," .
				"FULLTEXT KEY content (content)" .
				") ENGINE=MyISAM" .
				"");

				$this->resetIndex($content);
			} catch (Exception $e) {
				echo $e->getMessage();
				return;
			}
		}

		if (isset ($_GET['resetindex']) && $_GET['resetindex'] = 'kummer') {
			Aitsu_Db :: query("" .
			"truncate _aitsu_search ");
		}
	}

	public function setCategories($categories) {

		$this->categories = $categories;

		return $this;
	}

	public function setMask($mask) {

		$this->mask = $mask;

		return $this;
	}

	public function getSearchResult($searchTerm) {

		global $idcat;

		if (!isset ($this->categories)) {
			$this->categories = (string) $idcat;
		}

		if (!isset ($this->mask)) {
			$this->mask = '<dl><dt>{pagetitle}</dt><dd>{summary}</dd></dl>';
		}

		try {
			$results = Aitsu_Db :: fetchAll("" .
			"select " .
			"	artlang.idart, " .
			"	artlang.pagetitle, " .
			"	artlang.summary, " .
			"	date_format(artlang.lastmodified, '%d.%m.%y') as date, " .
			"	date_format(artlang.lastmodified, '%H:%m') as time, " .
			"	date_format(max(search.lastindexed), '%d.%m.%y %H:%m:%s') as indexof, " .
			"	search.content " .
			"from _aitsu_search search " .
			"left join _art_lang as artlang on search.idartlang = artlang.idartlang " .
			"left join _cat_art as catart on artlang.idart = catart.idart " .
			"left join _cat_tree as child on catart.idcat = child.idcat " .
			"left join _cat_tree as parent on child.lft between parent.lft and parent.rgt " .
			"left join _art_lang as target on artlang.idlang = target.idlang " .
			"where " .
			"	match (search.content) against (? in boolean mode) " .
			"	and parent.idcat in ({$this->categories}) " .
			"	and target.idartlang = ? " .
			"group by " .
			"	artlang.idart, " .
			"	artlang.pagetitle, " .
			"	artlang.summary, " .
			"	artlang.lastmodified " .
			"order by " .
			"	match (search.content) against (? in boolean mode) desc " .
			"limit 0, 20 " .
			"", array (
				$searchTerm,
				$this->idartlang,
				$searchTerm
			));

			$out = '';

			if (!empty ($results)) {
				foreach ($results as $result) {
					$tmpOut = $this->mask;
					foreach ($result as $key => $value) {
						$tmpOut = str_replace('{' . $key . '}', stripslashes($value), $tmpOut);
					}
					$out .= $tmpOut;
				}
			} else {
				return false;
			}

			return $out;

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
}