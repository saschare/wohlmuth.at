<?php


/**
 * Definition list
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Aitsu_Ee_Module_DefinitionList_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Definition list',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Outputs a definition list out of a list of line separated data.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725365-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$index = $context['index'];

		$list = Aitsu_Content_Text :: get('DefinitionList_' . $index, 0);

		$elements = array ();

		if (Aitsu_Registry :: isEdit() && empty ($list)) {
			$elements['DefinitionList'] = 'No entries have been made yet.';
		} else {
			if (preg_match_all('/^([^\\:]*)\\:\\s(.*)/m', $list, $matches) == 0) {
				return '';
			}
			for ($i = 0; $i < count($matches[0]); $i++) {
				$elements[$matches[1][$i]] = $matches[2][$i];
			}
		}

		$instance = new self();
		$view = $instance->_getView();
		$view->elements = $elements;

		return $view->render('index.phtml');
	}
}