<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * @Editor Frank Ammari, Ammari & Ammari GbR
 * 
 * {@id $Id: Class.php 18266 2010-08-23 10:07:27Z akm $}
 */

class Skin_Module_MetaTags_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'Meta tags',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns the meta tags of the current article.'),
			'type' => 'Header',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a072536f-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$instance = new self();
		Aitsu_Content_Edit :: noEdit('MetaTags', true);

		$output = '';
		if ($instance->_get('MetaTags', $output)) {
			return $output;
		}
		
		$idartlang = Aitsu_Registry :: get()->env->idartlang;

		$meta = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	* ' .
		'from ait_art_meta ' .
		'where ' .
		'	idartlang =' . $idartlang);
		
		if (isset (Aitsu_Registry :: get()->config->honeytrap->keyword)) {
			$honeyTraps = array_flip(Aitsu_Registry :: get()->config->honeytrap->keyword->toArray());
			if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
				$meta['robots'] = (object) array (
					'value' => 'noindex'
				);
			}
		}

		$view = $instance->_getView();
		$view->meta = $meta;

		$output = $view->render('index.phtml');

		$instance->_save($output, 'eternal');

		return $output;
	}
}