<?php


/**
 * Aitsu utilities.
 * 
 * @author Anreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Util.php 16857 2010-06-04 14:58:26Z akm $}
 */

class Aitsu_Util {

	protected function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function getUploadContent($client) {

		return Aitsu_Db :: fetchCol("" .
		"select concat(dirname, filename) from _upl " .
		"where " .
		"	idclient = ? " .
		"order by " .
		"	dirname asc, filename asc ", array (
			$client
		));
	}

	public static function getCategories($client, $lang) {

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	catlang.idcat as idcat, ' .
		'	catlang.name as catname, ' .
		'	count(parent.idcat) as level ' .
		'from ' .
		'	_cat as node ' .
		'	join _cat as parent ' .
		'	join _cat_lang as catlang ' .
		'where ' .
		'	node.lft between parent.lft and parent.rgt ' .
		'	and node.idcat = catlang.idcat ' .
		'	and parent.idclient = ? ' .
		'	and catlang.idlang = ? ' .
		'group by node.idcat ' .
		'order by node.lft ', array (
			$client,
			$lang
		));
	}

	public static function getTemplates($client, $regex = null) {

		$returnValue = array ();

		$templatePath = Aitsu_Db :: fetchOne("" .
		"select frontendpath from _clients " .
		"where " .
		"	idclient = ? " .
		"", array (
			$client
		));

		$templatePath .= '/templates';

		if ($handle = opendir($templatePath)) {
			while (false !== ($file = readdir($handle))) {
				if (is_file($templatePath . '/' . $file)) {
					if ($regex == null || preg_match($regex, $file)) {
						$returnValue[$file] = $file;
					}
				}
			}
		}

		ksort($returnValue);

		return $returnValue;
	}

	public static function getTemplate($client, $template) {

		$templatePath = Aitsu_Db :: fetchOne("" .
		"select frontendpath from _clients " .
		"where " .
		"	idclient = ? " .
		"", array (
			$client
		));

		return file_get_contents($templatePath . '/templates/' . $template);
	}

	public static function getShortcodeModules($client) {

		$modules = Aitsu_Db :: fetchCol("" .
		"select name from _mod " .
		"where " .
		"	idclient = ? " .
		"	and output like '%@availableAsShortCode%' " .
		"order by " .
		"	name asc " .
		"", $client);

		$returnValue = array ();

		if ($modules) {
			foreach ($modules as $module) {
				$returnValue[$module] = $module;
			}
		}

		return $returnValue;
	}

	public static function getCurrentUrl() {

		if (isset ($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			return 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	public static function getFileSystem($idclient, $searchfor, $types = null) {

		$returnValue = array ();

		$ofType = $types == null ? '' : 'and upload.filetype in (\'' . implode('\', \'', $types) . '\')';

		$files = Aitsu_Db :: fetchAll("" .
		"select " .
		"	concat(upload.dirname, upload.filename) as path, " .
		"	metadata.medianame as name " .
		"from _upl as upload " .
		"left join _upl_meta as metadata on upload.idupl = metadata.idupl " .
		"where " .
		"	(" .
		"		concat(upload.dirname, upload.filename) like ? " .
		"		or metadata.medianame like ? " .
		"		or metadata.description like ? " .
		"		or metadata.keywords like ? " .
		"	)" .
		"	and upload.idclient = ? " .
		"	{$ofType} " .
		"limit 0, 10 " .
		"", array (
			"%$searchfor%",
			"%$searchfor%",
			"%$searchfor%",
			"%$searchfor%",
			$idclient
		));

		if ($files) {
			foreach ($files as $file) {
				if ($types == null) {
					$returnValue[] = array (
						'text' => $file['path'],
						'extra' => urldecode($file['name']),
						'image' => '/image/32/32/1/' . $file['path']
					);
				} else {
					$returnValue[] = array (
						'text' => $file['path'],
						'extra' => urldecode($file['name']),
						'image' => '/image/32/32/1/' . $file['path']
					);

				}
			}
		}

		return $returnValue;
	}

	public static function getArticles($idlang, $searchfor) {

		$returnValue = array ();

		$searchfor = '%' . $searchfor . '%';

		$results = Aitsu_Db :: fetchAll("" .
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
		"		from _cat_tree as node " .
		"		left join _cat_tree as parent on node.lft between parent.lft and parent.rgt " .
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
		"limit 0, 20 " .
		"", array (
			$idlang,
			$searchfor,
			$searchfor,
			$searchfor,
			$searchfor
		));

		if (!$results) {
			return $returnValue;
		}

		foreach ($results as $result) {
			$pageTitle = htmlentities(stripslashes($result['pagetitle']));
			$url = $result['url'] != null ? $result['url'] : '';
			$name = '<span class="aitsu_pagetitle">' . $pageTitle . '</span><br /><span class="aitsu_url">' . $result['url'] . '.html</span>';
			$returnValue[] = array (
				'id' => $result['idart'],
				'name' => $name
			);
		}

		return $returnValue;
	}

	public static function tablePrefix($query) {

		$prefix = Aitsu_Registry :: get()->config->database->params->tblprefix;

		if (Aitsu_Registry :: get()->config->productionMode) {
			$query = str_replace('con_aitsu_article_property', 'con_aitsu_article_property_p', $query);
			$query = str_replace('con_aitsu_generic_persistence', 'con_aitsu_generic_persistence_p', $query);
			$query = str_replace('con_art_lang', 'con_art_lang_p', $query);
			$query = str_replace('con_content', 'con_content_p', $query);
		}

		if ($prefix == null || $prefix == 'con_') {
			return $query;
		}

		return preg_replace('/([^a-zA-Z\\.]|^)con_/', "$1$prefix", $query);
	}

	/**
	 * Iterates through all output buffering levels, stops the buffering
	 * and cleans the buffer.
	 * @return Void 
	 */
	public static function endAndCleanOutputBuffering() {

		while (ob_get_level() > 0) {
			ob_end_clean();
		}
	}

	public static function getAlias($alias) {

		/*
		 * First all is set to lower case and some special characters
		 * are replaced.
		 */
		$alias = str_replace(array (
			'ä',
			'ö',
			'ü',
			'Ä',
			'Ö',
			'Ü',
			'è',
			'é',
			'à',
			'ê',
			'ç',
			'ß'
		), array (
			'ae',
			'oe',
			'ue',
			'ae',
			'oe',
			'ue',
			'e',
			'e',
			'a',
			'e',
			'c',
			'ss'
		), (trim($alias)));

		/*
		 * Then we replace all non-character and non-decimal values
		 * with a minus.
		 */
		$alias = preg_replace('/[^a-zA-Z0-9_]{1,}/', '-', $alias);

		/*
		 * And we have to prevent minus at the very beginning and at
		 * the end of the alias.
		 */
		$alias = preg_replace('/^-*|-*$/', '', $alias);

		return strtolower($alias);
	}

	public static function parseConfig($config) {

		$returnValue = array ();

		if (preg_match_all('/^([^=]*)=(.*)$/m', $config, $matches) == 0) {
			return $returnValue;
		}

		for ($i = 0; $i < count($matches[0]); $i++) {
			$returnValue[trim($matches[1][$i])] = trim($matches[2][$i]);
		}

		return $returnValue;
	}

	public static function alt(& $value, $alt = null) {

		if (!isset ($value)) {
			return $alt;
		}

		return $value;
	}

	public static function parseSimpleIni($text, $base = null) {

		if (preg_match_all("/([^\\s*=]*)\\s*=\\s*([\"']?)(.*)\\2/", $text, $matches) == 0) {
			return (object) array ();
		}

		$return = $base == null ? (object) array () : $base;

		for ($i = 0; $i < count($matches[0]); $i++) {
			$current = & $return;
			$parts = explode('.', $matches[1][$i]);
			for ($j = 0; $j < count($parts); $j++) {
				if (!isset ($current-> {
					$parts[$j] })) {
					$current-> {
						$parts[$j] }
					= (object) array ();
				}
				$current = & $current-> {
					$parts[$j] };
				if ($j == count($parts) - 1) {
					if (strtolower($matches[3][$i]) == 'true') {
						$current = true;
					} elseif (strtolower($matches[3][$i]) == 'false') {
						$current = false;
					} else {
						$current = (strlen($matches[2][$i]) > 0) ? $matches[3][$i] : trim($matches[3][$i]);
					}	
				}
			}
		}

		return $return;
	}
}