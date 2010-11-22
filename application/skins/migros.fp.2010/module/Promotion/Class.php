<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19566 2010-10-25 13:19:55Z akm $}
 */

class Skin_Module_Promotion_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('Promotion', true);
		
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$idcat = isset($params->idcat) ? $params->idcat : Aitsu_Registry :: get()->env->idcat;
		$limit = isset($params->limit) ? $params->limit : 3;
		$class = isset($params->class) ? $params->class : null;

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('Promotion_' . $idcat . '_' . $limit, $output)) {
			return $output;
		}

		$view->articles = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	artlang.idart as idart, ' .
		'	artlang.teasertitle as title, ' .
		'	artlang.summary as teaser, ' .
		'	prop.textvalue as image ' .
		'from _cat as parent ' .
		'left join _cat as child on child.lft between parent.lft and parent.rgt and parent.idclient = child.idclient ' .
		'left join _cat_art as catart on child.idcat = catart.idcat ' .
		'left join _art_lang as artlang on catart.idart = artlang.idart ' .
		'left join _tag_art as tagart on artlang.idart = tagart.idart ' .
		'left join _tag as tag on tagart.tagid = tag.tagid ' .
		'left join _aitsu_property as propt on propt.identifier = :property ' .
		'left join _aitsu_article_property as prop on prop.idartlang = artlang.idartlang and propt.propertyid = prop.propertyid ' .
		'where ' .
		'	parent.idcat = :idcat ' .
		'	and artlang.idlang = :idlang ' .
		'	and tag.tag = :promotag ' .
		'	and (artlang.pubfrom is null or artlang.pubfrom < now()) ' .
		'	and (artlang.pubuntil is null or artlang.pubuntil + 1 > now()) ' .
		'order by ' .
		'	artlang.pubfrom asc ' .
		'limit 0, ' . $limit, array (
			':idcat' => $idcat,
			':idlang' => Aitsu_Registry :: get()->env->idlang,
			':promotag' => 'promotion',
			':property' => 'articleProperty:mainImage'
		));
		
		$view->class = $class;

		$output = $view->render('index.phtml');

		$instance->_save($output, 60 * 24 * 24);

		return $output;
	}

}