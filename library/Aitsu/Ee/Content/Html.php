<?php


/**
 * HTML content with inherition.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Html.php 16731 2010-05-31 12:12:50Z akm $}
 */

class Aitsu_Ee_Content_Html extends Aitsu_Content_Html {

	public static function get($token, $inherit = false, $words = 50) {
		
		$currentContent = Aitsu_Content :: get($token, Aitsu_Content :: HTML, null, null, $words);

		if ($inherit == true) {

			$content = Aitsu_Db :: fetchOne('' .
			'select content.value from _article_content as content ' .
			'where ' .
			'	content.idartlang = ? ' .
			'	and content.index = ? ', array (
				Aitsu_Registry :: get()->env->idartlang,
				$token
			));

			if (empty ($content)) {
				$content = Aitsu_Db :: fetchRow('' .
				'select ' .
				'	content.value, ' .
				'	artlang.idart ' .
				'from ' .
				'	_art_lang as cartlang, ' .
				'	_cat_art as catart, ' .
				'	_cat as node, ' .
				'	_cat as parent, ' .
				'	_cat_lang as catlang, ' .
				'	_art_lang as artlang, ' .
				'	_article_content as content ' .
				'where ' .
				'	cartlang.idartlang = ? ' .
				'	and catart.idart = cartlang.idart ' .
				'	and node.idcat = catart.idcat ' .
				'	and node.lft between parent.lft and parent.rgt ' .
				'	and catlang.idcat = parent.idcat ' .
				'	and catlang.idlang = cartlang.idlang ' .
				'	and artlang.idartlang = catlang.startidartlang ' .
				'	and content.idartlang = artlang.idartlang ' .
				'	and content.index = ? ' .
				'	and artlang.online = 1 ' .
				'	and content.value is not null ' .
				'	and content.value != \'\' ' .
				'order by ' .
				'parent.lft desc', array (
					Aitsu_Registry :: get()->env->idartlang,
					$token
				));
				
				if (!empty($content)) {
					return stripslashes($content['value']);
				}
			}
		}

		return $currentContent;
	}
}