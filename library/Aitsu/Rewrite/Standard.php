<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Rewrite_Standard implements Aitsu_Rewrite_Interface {

	protected function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function registerParams() {

		if (!isset ($_GET['url'])) {
			return;
		}

		if (substr($_GET['url'], -5) == '.html') {
			/*
			 * Page name given.
			*/
			$pathInfo = pathinfo($_GET['url']);
			$url = $pathInfo['dirname'];
			$urlname = $pathInfo['filename'];
		} else {
			if (substr($_GET['url'], -1) == '/') {
				$url = substr($_GET['url'], 0, -1);
			} else {
				$url = $_GET['url'];
			}
			$urlname = null;
		}

		$startCat = Aitsu_Registry :: get()->config->sys->startcat;
		$language = Aitsu_Registry :: get()->config->sys->language;
		$client = Aitsu_Registry :: get()->config->sys->client;

		if (empty ($url)) {
			/*
			 * Empty URL. Resolution is made based on configured values.
			 * 
			 * The start category as well as the language id are specific
			 * for a particular client. So the clientid is not used in
			 * the query.
			 */
			$result = Aitsu_Db :: fetchRow('' .
			'select ' .
			'	artlang.*, ' .
			'	catlang.idcat, ' .
			'	cat.idclient ' .
			'from _art_lang as artlang ' .
			'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
			'left join _cat as cat on catlang.idcat = cat.idcat ' .
			'where ' .
			'	catlang.idcat = :idcat ' .
			'	and artlang.idlang = :idlang', array (
				':idcat' => $startCat,
				':idlang' => $language
			));
		}
		elseif (Aitsu_Registry :: get()->config->rewrite->uselang && preg_match('@^\\w*/?$@', $url)) {
			/*
			 * Use lang is set to true. The first segment of the URL is used to resolve
			 * the language.
			 */
			$result = Aitsu_Db :: fetchRow('' .
			'select ' .
			'	artlang.*, ' .
			'	catlang.idcat, ' .
			'	lang.idclient ' .
			'from _art_lang as artlang ' .
			'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
			'left join _lang as lang on catlang.idlang = lang.idlang ' .
			'where ' .
			'	catlang.idcat = :idcat ' .
			'	and lang.name = :langname ' .
			'	and lang.idclient = :client ', array (
				':idcat' => $startCat,
				':langname' => $url,
				':client' => $client
			));
		} else {
			/*
			 * Normal URL resolution.
			 */
			if ($urlname == null) {
				/*
				 * ...using category only.
				 */
				$result = Aitsu_Db :: fetchRow('' .
				'select ' .
				'	artlang.*, ' .
				'	catlang.idcat, ' .
				'	cat.idclient ' .
				'from _art_lang as artlang ' .
				'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
				'left join _cat as cat on catlang.idcat = cat.idcat ' .
				'where ' .
				'	catlang.url = :url ' .
				'	and cat.idclient = :client ', array (
					':url' => $url,
					':client' => $client
				));
			} else {
				/*
				 * ...using category and page name.
				 */
				$result = Aitsu_Db :: fetchRow('' .
				'select ' .
				'	artlang.*, ' .
				'	catlang.idcat, ' .
				'	cat.idclient ' .
				'from _art_lang as artlang ' .
				'left join _cat_art as catart on artlang.idart = catart.idart ' .
				'left join _cat_lang as catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
				'left join _cat as cat on catlang.idcat = cat.idcat ' .
				'where ' .
				'	artlang.urlname = :urlname ' .
				'	and catlang.url = :url ' .
				'	and cat.idclient = :client ', array (
					':urlname' => $urlname,
					':url' => $url,
					':client' => $client
				));
			}
		}

		if ($result) {
			Aitsu_Registry :: get()->env->idart = $result['idart'];
			Aitsu_Registry :: get()->env->idcat = $result['idcat'];
			Aitsu_Registry :: get()->env->idlang = $result['idlang'];
			Aitsu_Registry :: get()->env->idartlang = $result['idartlang'];
			Aitsu_Registry :: get()->env->idclient = $result['idclient'];
			return;
		} else {
			Aitsu_Registry :: get()->env->idlang = Aitsu_Registry :: get()->config->sys->language;
		}

		if (Aitsu_Registry :: get()->config->rewrite->uselang) {
			/*
			 * As we have not been able to resolve the url and uselang is set to true,
			 * we have to determine the language id to enable the 404 handler
			 * to show the error page in the right language.
			*/
			$idlang = Aitsu_Db :: fetchOne('' .
			'select idlang from _lang ' .
			'where name = :name ', array (
				':name' => strtok($_GET['url'], '/')
			));

			if ($idlang) {
				Aitsu_Registry :: get()->env->idlang = $idlang;
			}
		}
	}

	public function rewriteOutput($html) {

		if (!isset (Aitsu_Registry :: get()->config->rewrite->modrewrite) || Aitsu_Registry :: get()->config->rewrite->modrewrite == false) {
			return $html;
		}

		$this->_populateMissingUrls();

		$matches = null;
		if (preg_match_all('/\\{ref:(idcat|idart)\\-(\\d+)\\}/s', $html, $matches) == 0) {
			return $html;
		}

		$baseUrl = (isset (Aitsu_Registry :: get()->env->sys->webpath) && Aitsu_Application_Status :: isStructured()) ? Aitsu_Registry :: get()->env->sys->webpath : '/';

		$idarts = array ();
		$idcats = array ();
		for ($i = 0; $i < count($matches[0]); $i++) {
			$ph = $matches[0][$i];
			if ($matches[1][$i] == 'idart') {
				$idarts[$matches[2][$i]][] = $matches[0][$i];
			}
			elseif ($matches[1][$i] == 'idcat') {
				$idcats[$matches[2][$i]][] = $matches[0][$i];
			}
		}

		if (!empty ($idarts)) {
			/*
			 * Replace links based on idart.
			*/
			$inClause = implode(',', array_keys($idarts));
			$results = Aitsu_Db :: fetchAll('' .
			'select ' .
			'	artlang.idart as idart, ' .
			'	concat(catlang.url, \'/\', artlang.urlname, \'.html\') as url ' .
			'from _art_lang as artlang ' .
			'inner join _cat_art as catart on artlang.idart = catart.idart ' .
			'inner join _cat_lang as catlang on (artlang.idlang = catlang.idlang and catart.idcat = catlang.idcat)' .
			'where ' .
			'	artlang.idart in (' . $inClause . ') ' .
			'	and artlang.idlang = :idlang', array (
				':idlang' => Aitsu_Registry :: get()->env->idlang
			));

			if ($results) {
				foreach ($results as $row) {
					foreach ($idarts[$row['idart']] as $placeHolder) {
						$html = str_replace($placeHolder, $baseUrl . $row['url'], $html);
					}
				}
			}
		}

		if (!empty ($idcats)) {
			/*
			 * Replace links based on idcat.
			*/
			$inClause = implode(',', array_keys($idcats));
			$results = Aitsu_Db :: fetchAll('' .
			'select ' .
			'	catlang.idcat as idcat, ' .
			'	catlang.url as url ' .
			'from _cat_lang as catlang ' .
			'where ' .
			'	catlang.idcat in (' . $inClause . ') ' .
			'	and catlang.idlang = :idlang ', array (
				':idlang' => Aitsu_Registry :: get()->env->idlang
			));

			if ($results) {
				foreach ($results as $row) {
					foreach ($idcats[$row['idcat']] as $placeHolder) {
						$html = str_replace($placeHolder, $baseUrl . $row['url'] . '/', $html);
					}
				}
			}
		}

		if (preg_match_all('/\\{ref:(idcat|idart)\\-(\\d+)\\}/s', $html, $matches) == 0) {
			return $html;
		}

		/*
		 * There are unreplaced references, which have to be eliminated.
		*/
		$html = preg_replace('/\\{ref:(idcat|idart)\\-(\\d+)\\}/s', '/', $html);

		return $html;
	}

	protected function _populateMissingUrls() {

		$idlang = Aitsu_Registry :: get()->env->idlang;

		if (Aitsu_Db :: fetchOne('' .
			'select count(idcat) from _cat_lang ' .
			'where url is null and idlang = :idlang', array (
				':idlang' => $idlang
			)) == 0) {
			/*
			 * There are no categories without an url in the current language.
			 */
			return;
		}

		Aitsu_Db :: startTransaction();

		try {
			if (Aitsu_Config :: get('rewrite.omitfirst')) {
				/*
				 * Omit first evaluates to true. This is the normal case.
				 */
				Aitsu_Db :: query('' .
				'update ' .
				'	_cat_lang catlang, ' .
				'	( ' .
				'		select ' .
				'			child.idcat idcat, ' .
				'			group_concat(catlang.urlname order by parent.lft asc separator \'/\') url ' .
				'		from _cat as child ' .
				'		left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
				'		left join _cat_lang as catlang on parent.idcat = catlang.idcat and parent.parentid > 0 ' .
				'		where catlang.idlang = :idlang ' .
				'		group by child.idcat ' .
				'	) url ' .
				'set catlang.url = url.url ' .
				'where ' .
				'	catlang.idcat = url.idcat ' .
				'	and catlang.idlang = :idlang', array (
					':idlang' => $idlang
				));
			} else {
				/*
				 * First level must remain. Unusual, but allowed.
				 */
				Aitsu_Db :: query('' .
				'update ' .
				'	_cat_lang catlang, ' .
				'	( ' .
				'		select ' .
				'			child.idcat idcat, ' .
				'			group_concat(catlang.urlname order by parent.lft asc separator \'/\') url ' .
				'		from _cat as child ' .
				'		left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
				'		left join _cat_lang as catlang on parent.idcat = catlang.idcat ' .
				'		where catlang.idlang = :idlang ' .
				'		group by child.idcat ' .
				'	) url ' .
				'set catlang.url = url.url ' .
				'where ' .
				'	catlang.idcat = url.idcat ' .
				'	and catlang.idlang = :idlang', array (
					':idlang' => $idlang
				));
			}

			/*
			 * If there are categories with null as url value, they are on top level
			 * and therefore will get an empty url.
			 */
			Aitsu_Db :: query('' .
			'update _cat_lang set url = \'\' where idlang = :idlang and url is null', array (
				':idlang' => $idlang
			));

			if (Aitsu_Config :: get('rewrite.uselang')) {
				/*
				 * Uselang evaluates to true. We therefore have to add the language
				 * code in front of each resulting url.
				 */
				Aitsu_Db :: query('' .
				'update _cat_lang catlang, _lang lang ' .
				'set catlang.url = concat(lang.name, \'/\', catlang.url) ' .
				'where catlang.idlang = :idlang', array (
					':idlang' => $idlang
				));
			}

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			trigger_error('Exception in ' . __FILE__ . ' on line ' . __LINE__);
			trigger_error('Message: ' . $e->getMessage());
			trigger_error($e->getTraceAsString());
		}
	}
}