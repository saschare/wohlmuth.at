<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2010, w3concepts AG
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR 
 */

class Skin_Module_MetaTags_Class extends Aitsu_Ee_Module_Abstract {
	
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