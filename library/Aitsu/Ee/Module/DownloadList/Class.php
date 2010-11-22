<?php


/**
 * Definition list
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright © 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17663 2010-07-21 13:30:22Z akm $}
 */

class Aitsu_Ee_Module_DownloadList_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'Download list',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns a list of files specified in a configuration.'),
			'type' => 'List',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725366-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();
		$index = $context['index'];

		$output = '';
		if ($instance->_get('DownloadList_' . $index, $output)) {
			return $output;
		}

		$elements = Aitsu_Ee_Config_Files :: set($index, 'DownloadListElements', '', 'Files', $pattern = '*');
		$elements = Aitsu_Core_File :: getByFilename($elements);

		if (empty ($elements) && Aitsu_Registry :: isEdit()) {
			return 'The download list does not contain any values yet.';
		}

		$view = $instance->_getView();
		$view->elements = $elements;

		$output = $view->render('index.phtml');
		$instance->_save($output, 'eternal');

		return $output;
	}
}