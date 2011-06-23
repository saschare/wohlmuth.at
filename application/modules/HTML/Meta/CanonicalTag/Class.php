<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Meta_CanonicalTag_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {
		
		Aitsu_Content_Edit :: noEdit('HTML.Meta.CanonicalTag', true);
		
		$instance = new self();
		
		$output = '';
		if ($instance->_get('CanonicalTag', $output)) {
			return $output;
		}
		
		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();
		$base = substr(Aitsu_Config :: get('sys.webpath'), 0, -1);
		
		if ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="' . $base . '{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="' . $base . '{ref:idart-' . $art->idart . '}" />';
		}
		
		$instance->_save($output, 'eternal');
		
		return $output;
	}

}