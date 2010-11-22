<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19583 2010-10-26 15:50:51Z akm $}
 */

class Skin_Module_Aktuell_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('Aktuell', true);
		
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$idcat = isset($params->idcat) ? $params->idcat : Aitsu_Registry :: get()->env->idcat;
		$limit = isset($params->limit) ? $params->limit : 3;

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('Aktuell_' . $idcat, $output)) {
			return $output;
		}

		$view->articles = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idart as idart, ' .
		'	artlang.teasertitle as title, ' .
		'	artlang.summary as teaser, ' .
		'	prop.textvalue as image ' .
		'from _art_lang as artlang ' .
		'left join _tag_art as tagart on artlang.idart = tagart.idart ' .
		'left join _tag as tag on tagart.tagid = tag.tagid ' .
		'left join _aitsu_property as propt on propt.identifier = :property ' .
		'left join _aitsu_article_property as prop on prop.idartlang = artlang.idartlang and propt.propertyid = prop.propertyid ' .
		'where ' .
		'	artlang.idlang = :idlang ' .
		'	and tag.tag = :aktuell ' .
		'	and (artlang.pubfrom is null or artlang.pubfrom < now()) ' .
		'	and (artlang.pubuntil is null or artlang.pubuntil + 1 > now()) ' .
		'order by ' .
		'	artlang.pubfrom asc ' .
		'limit 0, ' . $limit, array (
			':idlang' => Aitsu_Registry :: get()->env->idlang,
			':aktuell' => 'aktuell',
			':property' => 'articleProperty:mainImage'
		));

		$output = $view->render('index.phtml');

		$instance->_save($output, 60 * 24 * 24);

		return $output;
	}

}