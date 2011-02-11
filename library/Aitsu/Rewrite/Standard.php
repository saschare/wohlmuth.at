<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: Standard.php 19411 2010-10-20 10:42:12Z akm $}
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

		if (empty ($url)) {
			/*
			 * Empty URL. Resolution is made based on configured values.
			*/
			$result = Aitsu_Db :: fetchRow('' .
			'select artlang.*, catlang.idcat, cat.idclient from _art_lang as artlang ' .
			'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
			'left join _cat as cat on catlang.idcat = cat.idcat ' .
			'where ' .
			'	catlang.idcat = :idcat ' .
			'	and artlang.idlang = :idlang', array (
				':idcat' => Aitsu_Registry :: get()->config->sys->startcat,
				':idlang' => Aitsu_Registry :: get()->config->sys->language
			));
		}
		elseif (Aitsu_Registry :: get()->config->rewrite->uselang && preg_match('@^\\w*/?$@', $url)) {
			$result = Aitsu_Db :: fetchRow('' .
			'select artlang.*, catlang.idcat, lang.idclient from _art_lang as artlang ' .
			'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
			'left join _lang as lang on catlang.idlang = lang.idlang ' .
			'where ' .
			'	catlang.idcat = :idcat ' .
			'	and lang.name = :langname', array (
				':idcat' => Aitsu_Registry :: get()->config->sys->startcat,
				':langname' => $url
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
				'select artlang.*, catlang.idcat, cat.idclient from _art_lang as artlang ' .
				'left join _cat_lang as catlang on artlang.idartlang = catlang.startidartlang ' .
				'left join _cat as cat on catlang.idcat = cat.idcat ' .
				'where catlang.url = :url', array (
					':url' => $url
				));
			} else {
				/*
				 * ...using category and page name.
				*/
				$result = Aitsu_Db :: fetchRow('' .
				'select artlang.*, catlang.idcat, cat.idclient from _art_lang as artlang ' .
				'left join _cat_art as catart on artlang.idart = catart.idart ' .
				'left join _cat_lang as catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
				'left join _cat as cat on catlang.idcat = cat.idcat ' .
				'where ' .
				'	artlang.urlname = :urlname ' .
				'	and catlang.url = :url', array (
					':urlname' => $urlname,
					':url' => $url
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
		
		$baseUrl = (isset(Aitsu_Registry :: get()->env->sys->webpath) && Aitsu_Application_Status :: isStructured()) ? Aitsu_Registry :: get()->env->sys->webpath : '/';

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

		$results = Aitsu_Db :: fetchCol('' .
		'select idcat from _cat_lang where url is null and idlang = :idlang', array (
			':idlang' => $idlang
		));

		if (!$results) {
			/*
			 * There are no categories without an url in the current language.
			*/
			return;
		}

		Aitsu_Db :: startTransaction();

		try {
			$urlBase = '';

			if (isset (Aitsu_Registry :: get()->config->rewrite->uselang) && Aitsu_Registry :: get()->config->rewrite->uselang) {
				$langName = Aitsu_Db :: fetchOne('' .
				'select name from _lang where idlang = :idlang', array (
					':idlang' => $idlang
				));
				$urlBase .= $langName . '/';
			}

			foreach ($results as $idcat) {
				$url = Aitsu_Db :: fetchOne('' .
				'select group_concat(catlang.urlname order by parent.lft asc separator \'/\') as url ' .
				'from _cat as child ' .
				'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
				'left join _cat_lang as catlang on parent.idcat = catlang.idcat ' .
				'where ' .
				'	child.idcat = :idcat ' .
				'	and catlang.idlang = :idlang ' .
				'group by child.idcat', array (
					':idcat' => $idcat,
					':idlang' => $idlang
				));

				if ($url) {
					if (isset (Aitsu_Registry :: get()->config->rewrite->omitfirst) && Aitsu_Registry :: get()->config->rewrite->omitfirst) {
						$index = strpos($url, '/');
						if ($index) {
							$url = substr($url, $index +1);
						}
					}

					Aitsu_Db :: query('' .
					'update _cat_lang set url = :url ' .
					'where ' .
					'	idcat = :idcat ' .
					'	and idlang = :idlang', array (
						':url' => $urlBase . $url,
						':idcat' => $idcat,
						':idlang' => $idlang
					));
				}
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