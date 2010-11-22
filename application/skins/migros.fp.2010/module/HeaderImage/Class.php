<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Skin_Module_HeaderImage_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$instance = new self();
		$view = $instance->_getView();

		$output = '';
		if ($instance->_get('HeaderImage_' . $index, $output)) {
			return $output;
		}

		$choices = array ();
		if (isset($params->image)) {
			foreach ($params->image as $key => $value) {
				$choices[$value->name] = $value->path;
			}
		}
		$choices[Aitsu_Translate :: translate('Inherit from above')] = 'inheritFromAbove';
		$choices[Aitsu_Translate :: translate('Use article media')] = 'useArticleMedia';

		$medium = Aitsu_Ee_Config_Radio :: set($index, 'HeaderIllustration-Medium', '', $choices, Aitsu_Translate :: translate('Header'));
		$media = Aitsu_Ee_Config_Images :: set($index, 'HeaderIllustration-Media', '', Aitsu_Translate :: translate('Choose image'));

		if (empty ($medium)) {
			$medium = 'inheritFromAbove';
		}

		if ($medium == 'useArticleMedia') {
			$medium = $media;
		}

		if ($medium == 'inheritFromAbove') {
			$medium = $instance->_inheritFromAbove($index);
		}

		if (empty ($medium) && isset($params->image->a->path)) {
			$medium = $params->image->a->path;
		}

		$view->media = $medium;

		$output = $view->render('image.phtml');

		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 24 * 60 * 60);

		return $output;
	}

	protected function _inheritFromAbove($index) {

		$image = Aitsu_Db :: fetchRow('' .
		'select distinct ' .
		'	artlang.idartlang, ' .
		'	propa.textvalue as media, ' .
		'	propb.textvalue as medium ' .
		'from _cat as child ' .
		'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
		'left join _cat_lang as catlang on parent.idcat = catlang.idcat ' .
		'left join _art_lang as artlang on catlang.startidartlang = artlang.idartlang ' .
		'left join _aitsu_property as namea on namea.identifier = :namea ' .
		'left join _aitsu_article_property as propa on artlang.idartlang = propa.idartlang and namea.propertyid = propa.propertyid ' .
		'left join _aitsu_property as nameb on nameb.identifier = :nameb ' .
		'left join _aitsu_article_property as propb on artlang.idartlang = propb.idartlang and nameb.propertyid = propb.propertyid ' .
		'where ' .
		'	parent.idclient = :idclient ' .
		'	and child.idcat = :idcat ' .
		'	and catlang.idlang = :idlang ' .
		'	and propb.textvalue != \'inheritFromAbove\' ' .
		'order by ' .
		'	parent.lft desc', array (
			':idcat' => Aitsu_Registry :: get()->env->idcat,
			':idclient' => Aitsu_Registry :: get()->env->idclient,
			':idlang' => Aitsu_Registry :: get()->env->idlang,
			':namea' => 'ModuleConfig_' . $index . ':HeaderIllustration-Media',
			':nameb' => 'ModuleConfig_' . $index . ':HeaderIllustration-Medium'
		));

		if (!$image) {
			return null;
		}

		if ($image['medium'] == 'useArticleMedia') {			
			return Aitsu_Core_File :: getByMediaId(unserialize($image['media']));
		}

		return $image['medium'];
	}
}