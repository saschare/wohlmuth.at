<?php


/**
 * Article or category configuration.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ArticleOrCategory.php 16901 2010-06-09 05:45:54Z akm $}
 */

class Aitsu_Ee_Config_ArticleOrCategory extends Aitsu_Content_Config_Abstract {
	
	public function getTemplate() {

		return 'Category.phtml';
	}
	
	public static function set($index, $name, $label, $fieldset = '') {
		
		$instance = new self($index, $name);
		
		$instance->facts['fieldset'] = $fieldset;
		$instance->facts['label'] = $label;
		$instance->facts['type'] = 'text';
		
		return $instance->currentValue();
	}
}