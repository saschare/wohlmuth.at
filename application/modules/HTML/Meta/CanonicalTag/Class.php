<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Meta_CanonicalTag_Class extends Aitsu_Module_Abstract {

	protected function _init() {
		
		Aitsu_Content_Edit :: noEdit('HTML.Meta.CanonicalTag', true);
		
		$output = '';
		if ($this->_get('CanonicalTag', $output)) {
			return $output;
		}
		
		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();
		
		$base = substr(Aitsu_Config :: get('sys.webpath'), 0, -1);
		$canonicalPath = Aitsu_Config :: get('sys.canonicalpath');
		if ($canonicalPath != null) {
			$base = substr(Aitsu_Config :: get('sys.canonicalpath'), 0, -1);
		}
		
		if ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="' . $base . '{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="' . $base . '{ref:idart-' . $art->idart . '}" />';
		}
		
		$this->_save($output, 'eternal');
		
		return $output;
	}

}