<?php


/**
 * Cross linking of articles.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: CrossLinking.php 16104 2010-04-23 09:13:12Z akm $}
 */

class Aitsu_Article_CrossLinking {

	protected $idart;

	protected function __construct($idart) {

		$this->idart = $idart;

		$this->db = Aitsu_Registry :: get()->db;
	}

	public static function getInstance($idart = null) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self($idart);
		}

		return $instance;
	}

	public function getEdit($edit, $idlang) {

		if (!$edit) {
			return '';
		}

		if (isset ($_POST['aitsuCrossLinks'])) {
			$this->_update();
		}

		$formid = uniqid();
		$id = uniqid();
		$url = Aitsu_Util :: getCurrentUrl() . '&method=article';

		$currentData = $this->_prePopulate($idlang);

		$prePopulate = '';
		if ($currentData) {
			$prePopulate = ', prePopulate: ' . json_encode($currentData);
		}

		$out = '<script type="text/javascript">$(document).ready(function () {$("#' . $id . '").tokenInput("' . $url . '", {hintText: "Type in article name, page title or part of summary",noResultsText: "No results",searchingText: "searching..."' . $prePopulate . '});});</script>';

		$out .= '<form class="aitsu_form" action="' . Aitsu_Util :: getCurrentUrl() . '" method="POST" name="form' . $formid . '">';

		$out .= '<fieldset><legend>CrossLinks</legend><div class="type-text">';
		$out .= '<input type="text" name="aitsuCrossLinks" class="aitsu_token" id="' . $id . '">';
		$out .= '</div></fieldset>';

		$out .= '<div class="type-button clearfix"><ul class="ui-widget ui-helper-clearfix right">';
		$out .= '<li class="ui-state-default ui-corner-all">';
		$out .= '<span class="ui-icon ui-icon-circle-check" onclick="document.form' . $formid . '.submit();"/>';
		$out .= '</li></ul></div>';

		$out .= '</form>';

		return $out;
	}

	protected function _update() {

		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idart = Aitsu_Registry :: get()->env->idart;

		Aitsu_Db :: query("" .
		"delete from _crosslink " .
		"where " .
		"	idartsrc = ? " .
		"	or idartdest = ? " .
		"", array (
			$idart,
			$idart
		));

		if (preg_match_all('/\\d{1,}/', $_POST['aitsuCrossLinks'], $matches) < 1) {
			return;
		}

		foreach ($matches[0] as $id) {
			Aitsu_Db :: query("" .
			"replace into _aitsu_crosslink " .
			"(idartsrc, idartdest, created) " .
			"values " .
			"(?, ?, now()) " .
			"", array (
				$idart,
				$id
			));
		}
	}

	public static function getEditData() {

		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idart = Aitsu_Registry :: get()->env->idart;

		return Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	article.idart, ' .
		'	article.url, ' .
		'	article.pagetitle ' .
		'from ( ' .
		'	select ' .
		'		artlang.idart as idart, ' .
		'		concat(\'/\', lower(catpath.path), \'/\', lower(artlang.urlname)) as url, ' .
		'		artlang.title as title, ' .
		'		artlang.pagetitle as pagetitle, ' .
		'		artlang.summary as summary ' .
		'	from _art_lang as artlang ' .
		'	left join _cat_art as catart on artlang.idart = catart.idart ' .
		'	left join ( ' .
		'		select ' .
		'			node.idcat as idcat, ' .
		'			convert(group_concat(catlang.urlname order by parent.rgt desc separator ' / ') using utf8) as path ' .
		'		from _cat as node ' .
		'		left join _cat as parent on node.lft between parent.lft and parent.rgt ' .
		'		left join _cat_lang as catlang on parent.idcat = catlang.idcat and catlang.idlang = ? ' .
		'		group by node.idcat ' .
		'		) as catpath on catpath.idcat = catart.idcat ' .
		'	where artlang.idlang = ? ' .
		'	) as article ' .
		'left join _crosslink as crosslink on article.idart = crosslink.idartsrc or article.idart = crosslink.idartdest ' .
		'where ' .
		'	( ' .
		'		crosslink.idartsrc = ? ' .
		'		or crosslink.idartdest = ? ' .
		'	) ' .
		'	and article.idart != ? ' .
		'order by ' .
		'	article.pagetitle asc, ' .
		'	article.url asc ', array (
			$idlang,
			$idlang,
			$idart,
			$idart,
			$idart
		));
	}

	protected function _prePopulate($idlang) {

		$db = self :: getInstance()->db;

		$returnValue = array ();

		$results = Aitsu_Db :: fetchAll("" .
		"select distinct " .
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
		"		from _cat_tree as node " .
		"		left join _cat_tree as parent on node.lft between parent.lft and parent.rgt " .
		"		left join _cat_lang as catlang on parent.idcat = catlang.idcat and catlang.idlang = ? " .
		"		group by " .
		"			node.idcat " .
		"		) as catpath on catpath.idcat = catart.idcat " .
		"	) as article " .
		"left join _aitsu_crosslink as crosslink on article.idart = crosslink.idartsrc or article.idart = crosslink.idartdest " .
		"where " .
		"	( " .
		"		crosslink.idartsrc = ? " .
		"		or crosslink.idartdest = ? " .
		"	) " .
		"	and article.idart != ? " .
		"order by " .
		"	article.pagetitle asc, " .
		"	article.url asc " .
		"", array (
			$idlang,
			$this->idart,
			$this->idart,
			$this->idart
		));

		if (!$results) {
			return $returnValue;
		}

		foreach ($results as $result) {
			$returnValue[] = array (
				'id' => $result['idart'],
				'name' => '<span class="aitsu_pagetitle">' . htmlentities(stripslashes($result['pagetitle'])) . '</span><br /><span class="aitsu_url">' . $result['url'] . '.html</span>'
			);
		}

		return $returnValue;
	}

	public static function getLinkedArticles() {

		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idart = Aitsu_Registry :: get()->env->idart;

		Aitsu_Db :: fetchAll("" .
		"select " .
		"	artlang.idart, " .
		"	artlang.pagetitle, " .
		"	artlang.summary  " .
		"from _crosslink as crosslink " .
		"left join _art_lang as artlang on crosslink.idartsrc = artlang.idart or crosslink.idartdest = artlang.idart " .
		"where " .
		"	( " .
		"		crosslink.idartsrc = ? " .
		"		or crosslink.idartdest = ? " .
		"	) " .
		"	and artlang.idart != ? " .
		"	and artlang.idlang = ? " .
		"	and artlang.online = 1 " .
		"order by " .
		"	artlang.pagetitle asc, " .
		"	artlang.title asc ", array (
			$idart,
			$idart,
			$idart,
			$idlang
		));
	}
}