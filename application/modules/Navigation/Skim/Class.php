<?php


/**
 * The module returns the preceding and succeeding article of the current
 * article filtering by following criteria...
 * 
 * - article is online
 * - category is visbible
 * - category is public
 * 
 * ...and taking into account the category's position and the articles' order
 * number (artsort).
 * 
 * Use either BackX oder ForthX as the index, where X represents the idcat of
 * the most top category the article must reside in. If using BackX the template
 * back.phtml is used to render the output, otherwise forth.phtml is used.
 * 
 * If the X is ommitted (i.e. using either Back or Forth as index), the skimming
 * remains within the current category.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Module_Navigation_Skim_Class extends Aitsu_Module_Abstract {

	protected $_isVolatile = true;
	protected $_allowEdit = false;

	protected function _main() {

		$view = $this->_getView();

		$template = strtolower(substr($this->_index, 0, 4)) == 'back' ? 'back' : 'forth';
		
		if (preg_match('/\\d*$/', $this->_index, $match)) {
			$startidcat = $match[0];
		} else {
			$startidcat = Aitsu_Registry :: get()->env->idcat;
		}

		$currentPosition = Aitsu_Db :: fetchOne('' .
		'select ' .
		'	current.position ' .
		'from ( ' .
		'	select ' .
		'		@rownum := @rownum + 1 as position, ' .
		'		a.idartlang ' .
		'	from (' .
		'		select distinct ' .
		'			artlang.* ' .
		'		from _art_lang artlang ' .
		'		left join _cat_art catart on artlang.idart = catart.idart ' .
		'		left join _art_lang lang on artlang.idlang = lang.idlang ' .
		'		left join _cat cat on catart.idcat = cat.idcat ' .
		'		left join _cat parent on cat.lft between parent.lft and parent.rgt ' .
		'		left join _cat_lang catlang on cat.idcat = catlang.idcat and lang.idlang = catlang.idlang ' .
		'		where ' .
		'			lang.idartlang = :idartlang ' .
		'			and artlang.online = 1 ' .
		'			and catlang.visible = 1 ' .
		'			and catlang.public = 1 ' .
		'			and parent.idcat = :startidcat ' .
		'		order by ' .
		'			cat.lft asc, ' .
		'			artlang.artsort asc ' .
		'		) a, ' .
		'	(' .
		'		select @rownum := 0' .
		'	) rownum ' .
		') current ' .
		'where ' .
		'	current.idartlang = :idartlang ', array (
			':idartlang' => Aitsu_Registry :: get()->env->idartlang,
			':startidcat' => $startidcat
		));

		$art = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	skim.* ' .
		'from ( ' .
		'	select ' .
		'		@rownum := @rownum + 1 as position, ' .
		'		a.* ' .
		'	from (' .
		'		select distinct ' .
		'			artlang.* ' .
		'		from _art_lang artlang ' .
		'		left join _cat_art catart on artlang.idart = catart.idart ' .
		'		left join _art_lang lang on artlang.idlang = lang.idlang ' .
		'		left join _cat cat on catart.idcat = cat.idcat ' .
		'		left join _cat parent on cat.lft between parent.lft and parent.rgt ' .
		'		left join _cat_lang catlang on cat.idcat = catlang.idcat and lang.idlang = catlang.idlang ' .
		'		where ' .
		'			lang.idartlang = :idartlang ' .
		'			and artlang.online = 1 ' .
		'			and catlang.visible = 1 ' .
		'			and catlang.public = 1 ' .
		'			and parent.idcat = :startidcat ' .
		'		order by ' .
		'			cat.lft asc, ' .
		'			artlang.artsort asc ' .
		'		) a, ' .
		'	(' .
		'		select @rownum := 0' .
		'	) rownum ' .
		') skim ' .
		'where ' .
		'	skim.position - 1 = :position ' .
		'	or skim.position + 1 = :position ' .
		'order by ' .
		'	skim.position asc ', array (
			':position' => $currentPosition,
			':idartlang' => Aitsu_Registry :: get()->env->idartlang,
			':startidcat' => $startidcat
		));

		$view->art = $art;

		return $view->render($template . '.phtml');
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}
}