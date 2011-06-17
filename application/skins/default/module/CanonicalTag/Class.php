<?php

/**
 * @author Frank Ammari, Ammari & Ammari GbR
 * @copyright Copyright &copy; 2011, Ammari & Ammari GbR
 */

class Skin_Module_CanonicalTag_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('CanonicalTag', true);
		
		$instance = new self();
		
		$output = '';
		if ($instance->_get('CanonicalTag', $output)) {
			return $output;
		}
		
		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();
		
		if (Aitsu_Registry :: get()->env->idcat == Aitsu_Registry :: get()->config->sys->startcat) {
			if(Aitsu_Registry :: get()->env->idlang == 2) {
				// in case of multilingual web page
				$output = '<link rel="canonical" href="/en/" />';
			} else {
				$output = '<link rel="canonical" href="/" />';
			}
		} elseif ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="{ref:idart-' . $art->idart . '}" />';
		}
		
		$instance->_save($output, 'eternal');
		
		return $output;
	}

}