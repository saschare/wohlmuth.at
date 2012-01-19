<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_View_Articles {

	public static function art($cat, $idlang, $syncLang) {

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idart, ' .
		'	artlang.idartlang, ' .
		'	artlang.title, ' .
		'	artlang.online, ' .
		'	if(catlang.startidartlang = artlang.idartlang, 1, 0) as isstart ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _cat_lang as catlang on artlang.idlang = catlang.idlang and catart.idcat = catlang.idcat ' .
		'where ' .
		'	catart.idcat = :cat ' .
		'	and artlang.idlang = :idlang ' .
		'order by ' .
		'	isstart desc, ' .
		'	artlang.title asc ', array (
			':cat' => $cat,
			':idlang' => $idlang
		));
	}

	public static function full($cat, $syncLang = null, $idlang = null) {

		$idlang = $idlang == null ? Aitsu_Registry :: get()->session->currentLanguage : $idlang;

		if ($syncLang != null) {			
			return Aitsu_Db :: fetchAll('' .
			'select ' .
			'	artlang.idart idart, ' .
			'	artlang.idartlang idartlang, ' .
			'	artlang.title title, ' .
			'	artlang.urlname urlname, ' .
			'	artlang.pagetitle pagetitle, ' .
			'	artlang.online online, ' .
			'	if (artlang.published = \'0000-00-00 00:00:00\', 0, 1) published, ' .
			'	if(catlang.startidartlang = artlang.idartlang, 1, 0) isstart, ' .
			'	if(oartlang.idartlang is null, 0, 1) synced, ' .
			'	artlang.artsort artsort ' .
			'from _art_lang as artlang ' .
			'left join _art_lang as sartlang on artlang.idart = sartlang.idart and sartlang.idlang = :syncLang ' .
			'left join _art_lang as oartlang on artlang.idart = oartlang.idart and oartlang.idlang = :idlang ' .
			'left join _cat_art as catart on artlang.idart = catart.idart ' .
			'left join _cat_lang as catlang on artlang.idlang = catlang.idlang and catart.idcat = catlang.idcat ' .
			'where ' .
			'	catart.idcat = :cat ' .
			'	and ( ' .
			'		sartlang.idartlang is null and oartlang.idartlang is not null ' .
			'		or sartlang.idartlang is not null and oartlang.idartlang is null ' .
			'		or artlang.idlang = :idlang ' .
			'		) ' .
			'group by ' .
			'	artlang.idart ' .
			'order by ' .
			'	artsort asc, ' .
			'	isstart desc, ' .
			'	artlang.title asc', array (
				':cat' => $cat,
				':idlang' => $idlang,
				':syncLang' => $syncLang
			));
		}

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idart idart, ' .
		'	artlang.idartlang idartlang, ' .
		'	artlang.title title, ' .
		'	artlang.urlname urlname, ' .
		'	artlang.pagetitle pagetitle, ' .
		'	artlang.online online, ' .
		'	if (pub.pubtime is null or artlang.lastmodified != pub.pubtime, 0, 1) published, ' .
		'	if(catlang.startidartlang = artlang.idartlang, 1, 0) isstart, ' .
		'	1 as synced, ' .
		'	artlang.artsort artsort ' .
		'from _art_lang as artlang ' .
		'left join _pub as pub on artlang.idartlang = pub.idartlang and pub.status = 1 ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'left join _cat_lang as catlang on artlang.idlang = catlang.idlang and catart.idcat = catlang.idcat ' .
		'where ' .
		'	catart.idcat = :cat ' .
		'	and artlang.idlang = :idlang ' .
		'order by ' .
		'	artsort asc, ' .
		'	isstart desc, ' .
		'	artlang.title asc', array (
			':cat' => $cat,
			':idlang' => $idlang
		));
	}
}