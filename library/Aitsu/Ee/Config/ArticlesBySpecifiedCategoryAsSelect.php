<?php


/**
 * Select configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ArticlesBySpecifiedCategoryAsSelect.php 16494 2010-05-20 08:45:23Z akm $}
 */

class Aitsu_Ee_Config_ArticlesBySpecifiedCategoryAsSelect extends Aitsu_Content_Config_Abstract {

	public function getTemplate() {

		return 'Select.phtml';
	}

	public static function set($index, $name, $label, $categories, $idlang) {

		$instance = new self($index, $name);

		$instance->facts['label'] = $label;

		$categories = !is_array($categories) ? array (
			0
		) : $categories;
		$categories = implode(',', $categories);

		$cats = Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	artlang.idartlang, ' .
		'	artlang.title ' .
		'from _art_lang as artlang ' .
		'left join _cat_art as catart on artlang.idart = catart.idart ' .
		'where ' .
		'	artlang.online = 1 ' .
		'	and catart.idcat in (' . $categories . ') ' .
		'	and artlang.idlang = ? ' .
		'order by artlang.title asc ', array (
			$idlang
		));
		
		if ($cats) {
			foreach ($cats as $cat) {
				$instance->params['keyValuePairs'][$cat['title']] = $cat['idartlang'];
			}
		}

		return $instance->currentValue();
	}
}