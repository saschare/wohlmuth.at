<?php


/**
 * Aitsu article.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Article.php 17367 2010-06-30 10:03:27Z akm $}
 */
 
class Aitsu_Core_Article {

	protected $db;
	protected $idartlang;
	protected $data = array ();

	protected function __construct($idartlang) {

		$this->idartlang = $idartlang;

		$this->_loadArtLang();
		$this->_loadArticleProperties();
		$this->_loadContent();
	}

	public static function factory($idartlang = null) {

		static $instance = array ();
		
		$idartlang = $idartlang == null ? Aitsu_Registry :: get()->env->idartlang : $idartlang;

		if (!isset ($instance[$idartlang])) {
			$instance[$idartlang] = new self($idartlang);
		}

		return $instance[$idartlang];
	}

	protected function _loadArtLang() {

		$this->data['artlang'] = Aitsu_Db :: fetchRow("" .
		"select * from _art_lang where idartlang = ? " .
		"limit 0, 1 ", array (
			$this->idartlang
		));
	}

	protected function _loadArticleProperties() {

		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	b.identifier, " .
		"	convert(if (a.textvalue is not null, a.textvalue, if (a.floatvalue is not null, a.floatvalue, a.datevalue)) using utf8) as value " .
		"from _aitsu_article_property as a " .
		"left join _aitsu_property as b on a.propertyid = b.propertyid " .
		"where a.idartlang = ? ", array (
			$this->idartlang
		));

		if (!$results) {
			$this->data['properties'] = array ();
			return;
		}

		foreach ($results as $result) {
			$this->data['properties'][$result['identifier']] = $result['value'];
		}
	}

	/**
	 * @deprecated 0.9.2 - 07.10.2010
	 */
	protected function _loadContent() {

		/*$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	typ.type, " .
		"	content.typeid, " .
		"	content.value " .
		"from _content as content " .
		"left join _type as typ on content.idtype = typ.idtype " .
		"where content.idartlang = ? ", array (
			$this->idartlang
		));

		if (!$results) {
			$this->data['content'] = array ();
			return;
		}

		foreach ($results as $result) {
			$value = $this->_replaceShortCodes(urldecode($result['value']));
			$this->data['content'][substr($result['type'], 4)][$result['typeid']] = $value;
		}*/
	}

	protected function _replaceShortCodes($content) {

		return preg_replace('/_\\[(.*?)\\:([^\\]]*)\\]/', "_[$1:$2:{$this->idartlang}]", $content);
	}

	public function get($key) {

		$segments = explode('/', $key);

		$data = $this->data;

		foreach ($segments as $segment) {
			if (!isset ($data[$segment])) {
				return null;
			}
			$data = $data[$segment];
		}

		return $data;
	}

	public function __get($key) {

		if ($key == 'content') {
			return $this->data;
		}

		if (isset($this->data['artlang']) && array_key_exists($key, $this->data['artlang'])) {
			return $this->data['artlang'][$key];
		}

		return null;
	}

	public function touch() {

		Aitsu_Db :: query("" .
		"update _art_lang set lastmodified = now() " .
		"where idartlang = ? ", array (
			$this->idartlang
		));
	}
	
	public static function searchFor($searchTerm, $idlang, $limit = 20) {
		
		$searchfor = '%' . $searchTerm . '%';

		return Aitsu_Db :: fetchAll("" .
		"select " .
		"	article.idart, " .
		"	article.url, " .
		"	article.pagetitle " .
		"from ( " .
		"	select " .
		"		artlang.idart as idart, " .
		"		concat('/', lower(catpath.path), '/', lower(artlang.urlname)) as url, " .
		"		artlang.title as title, " .
		"		artlang.pagetitle as pagetitle, " .
		"		artlang.summary as summary " .
		"	from _art_lang as artlang " .
		"	left join _cat_art as catart on artlang.idart = catart.idart " .
		"	left join ( " .
		"		select " .
		"			node.idcat as idcat, " .
		"			convert(group_concat(catlang.urlname order by parent.rgt desc separator '/') using utf8) as path " .
		"		from _cat as node " .
		"		left join _cat as parent on node.lft between parent.lft and parent.rgt " .
		"		left join _cat_lang as catlang on parent.idcat = catlang.idcat and catlang.idlang = ? " .
		"		group by " .
		"			node.idcat " .
		"		) as catpath on catpath.idcat = catart.idcat " .
		"	) as article " .
		"where " .
		"	article.url like ? " .
		"	or article.title like ? " .
		"	or article.pagetitle like ? " .
		"	or article.summary like ? " .
		"limit 0, {$limit} " .
		"", array (
			$idlang,
			$searchfor,
			$searchfor,
			$searchfor,
			$searchfor
		));
	}
}