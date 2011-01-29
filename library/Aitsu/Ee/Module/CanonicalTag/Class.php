<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_CanonicalTag_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		$instance = new self();
		
		$output = '';
		if ($instance->_get('CanonicalTag', $output)) {
			return $output;
		}
		
		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();
		
		if ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="{ref:idart-' . $art->idart . '}" />';
		}
		
		$instance->_save($output, 'eternal');
		
		return $output;
	}

}