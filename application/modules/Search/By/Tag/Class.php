<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Module_Search_By_Tag_Class extends Aitsu_Module_Tree_Abstract {

	protected $_allowEdit = false;
	protected $_isVolatile = true;

	protected function _init() {

		$this->_idSuffix = hash('md4', serialize($_GET));
	}

	protected function _main() {

		$maxTagCount = 48;

		$exclusions = array (
			'topic',
			'important'
		);

		/*
		 * Set current language.
		 */
		if (Aitsu_Application_Status :: isEdit()) {
			$lang = Aitsu_Registry :: get()->session->currentLanguage;
		} else {
			$lang = Aitsu_Registry :: get()->env->idlang;
		}

		$view = $this->_getView();
		$view->currentTags = array ();

		/*
		 * Make sure tags are transmitted as integer values only.
		 */
		$tags = $this->_getTags();

		/*
		 * Prepare dynamic parts of the queries.
		 */
		$on = array ();
		$clause = array ();
		$pageClause = array ();
		$values = array ();
		for ($i = 0; $i < count($tags); $i++) {
			$clause[$i] = 'and pages.tagid = :tag' . $i;
			$values[':tag' . $i] = $tags[$i];
			$on[$i] = (int) $tags[$i];
			$view->currentTags[] = $tags[$i];
		}

		/*
		 * Determine lft and rgt of the current category to restrict
		 * the navigation to the current category and its descents.
		 */
		$ns = (object) Aitsu_Db :: fetchRowC('eternal', '' .
		'select lft, rgt from _cat where idcat = :idcat', array (
			':idcat' => Aitsu_Registry :: get()->env->idcat
		), true);

		/*
		 * Determine the current tags.
		 */
		if (empty ($view->currentTags)) {
			$view->currentTagsFull = array ();
		} else {
			$view->currentTagsFull = Aitsu_Db :: fetchAllC('eternal', '' .
			'select ' .
			'	* ' .
			'from _tag ' .
			'where ' .
			'	tagid in (' . implode(',', $view->currentTags) . ') ' .
			'order by ' .
			'	tag', null, true);
		}

		/*
		 * Determine the current number of pages.
		 */
		if (empty ($tags)) {
			$currentNumberOfPages = PHP_INT_MAX;
		} else {
			$currentNumberOfPages = Aitsu_Db :: fetchOneC('eternal', '' .
			'select count(*) from (' .
			'	select distinct artlang.idart ' .
			'	from _art_lang artlang ' .
			'	left join _tag_art tagart on artlang.idart = tagart.idart ' .
			'	left join _cat_art catart on artlang.idart = catart.idart ' .
			'	left join _cat cat on catart.idcat = cat.idcat ' .
			'	where ' .
			'		tagart.tagid in (' . implode(',', $on) . ') ' .
			'		and artlang.online = 1 ' .
			'		and cat.lft between :lft and :rgt ' .
			'	group by ' .
			'		tagart.idart ' .
			'	having ' .
			'		count(distinct tagart.tagid) = :numberOfTags ' .
			') a', array (
				':numberOfTags' => count($on),
				':lft' => $ns->lft,
				':rgt' => $ns->rgt
			), true);
		}

		/*
		 * Determine the available tags.
		 */
		$view->tags = Aitsu_Db :: fetchAllC('eternal', '' .
		'select ' .
		'	tag.tagid, ' .
		'	tag.tag, ' .
		'	count(distinct art.idart) pages ' .
		'from (' .
		'	select ' .
		'		max(artlang.idart) idart ' .
		'	from _art_lang artlang ' .
		'	left join _tag_art pages on pages.idart = artlang.idart and pages.tagid in (' . (empty ($on) ? '0' : implode(',', $on)) . ') ' .
		'	left join _cat_art catart on artlang.idart = catart.idart ' .
		'	left join _cat cat on catart.idcat = cat.idcat ' .
		'	where ' .
		'		artlang.idlang = :idlang ' .
		'		and artlang.online = 1 ' .
		'		and cat.lft between :lft and :rgt ' .
		'	group by ' .
		'		artlang.idartlang ' .
		'	having ' .
		'		count(pages.idart) = :numberOfTags' .
		') art ' .
		'left join _tag_art tagart on art.idart = tagart.idart ' .
		'left join _tag tag on tagart.tagid = tag.tagid ' .
		'where ' .
		'	tag.tagid not in (' . (empty ($on) ? '0' : implode(',', $on)) . ') ' .
		'group by ' .
		'	tag.tagid, ' .
		'	tag.tag ' .
		'having ' .
		'	count(distinct art.idart) < :currentNumberOfPages ' .
		'order by ' .
		'	count(distinct art.idart) desc ' .
		'limit 0, ' . $maxTagCount, array (
			':idlang' => $lang,
			':numberOfTags' => count($on),
			':currentNumberOfPages' => $currentNumberOfPages,
			':lft' => $ns->lft,
			':rgt' => $ns->rgt
		), true);
		usort($view->tags, array (
			$this,
			'_sortTags'
		));

		/*
		 * Determine the pages to be displayed.
		 */
		if (empty ($tags)) {
			$view->pages = array ();
		} else {
			$view->pages = Aitsu_Db :: fetchAllC('eternal', '' .
			'select distinct ' .
			'	artlang.*, ' .
			'	catlang.idcat, ' .
			'	catlang.public ' .
			'from _art_lang artlang ' .
			'left join _tag_art pages on pages.idart = artlang.idart and pages.tagid in (' . implode(',', $on) . ') ' .
			'left join _cat_art catart on catart.idart = artlang.idart ' .
			'left join _cat cat on catart.idcat = cat.idcat ' .
			'left join _cat_lang catlang on catart.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
			'where ' .
			'	artlang.idlang = :idlang ' .
			'	and artlang.online = 1 ' .
			'	and cat.lft between :lft and :rgt ' .
			'group by ' .
			'	artlang.idartlang ' .
			'having ' .
			'	count(pages.idart) = :numberOfTags ' .
			'order by ' .
			'	artlang.pagetitle asc ' .
			'limit 0, 100', array (
				':idlang' => $lang,
				':numberOfTags' => count($on),
				':lft' => $ns->lft,
				':rgt' => $ns->rgt
			), true);
		}
		$view->pages = array_filter($view->pages, array (
			$this,
			'_isAllowedPage'
		));

		/*
		 * Render the result.
		 */
		return $view->render('index.phtml');
	}

	protected function _cachingPeriod() {

		/*
		 * Allow the result to be cached for at most one year.
		 */
		return 60 * 60 * 24 * 365;
	}

	private function _sortTags($a, $b) {

		return $a['tag'] > $b['tag'];
	}

	private function _isAllowedPage($page) {

		if ($page['public'] == 1) {
			return true;
		}

		$user = Aitsu_Adm_User :: getInstance();

		if ($user == null) {
			return false;
		}

		return $user->isAllowed(array (
			'language' => $page['idlang'],
			'resource' => array (
				'type' => 'cat',
				'id' => $page['idcat']
			)
		));
	}

	protected function _getTags() {

		if (!isset ($_REQUEST['tags']) || !is_array($_REQUEST['tags'])) {
			return array ();
		}

		return array_filter($_REQUEST['tags'], array (
			'Aitsu_Util_Type',
			'integer'
		));
	}
}
?>
