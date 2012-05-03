<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Meta_CanonicalTag_Class extends Aitsu_Module_Abstract {

	protected $_allowEdit = false;

	protected function _main() {

		$art = Aitsu_Persistence_Article :: factory(Aitsu_Registry :: get()->env->idart, Aitsu_Registry :: get()->env->idlang)->load();

		$base = substr(Aitsu_Config :: get('sys.webpath'), 0, -1);
		$canonicalPath = Aitsu_Config :: get('sys.canonicalpath');
		if ($canonicalPath != null) {
			$base = substr(Aitsu_Config :: get('sys.canonicalpath'), 0, -1);
		}

                if ($art->idcat == Aitsu_Config::get('sys.startcat')) {

                    $language = Aitsu_Persistence_Language::factory(Aitsu_Registry::get()->env->idlang)->name;
                    
                    if (Aitsu_Config::get('rewrite.uselang')) {
                        $output = '<link rel="canonical" href="' . $base . '/' . $language . '/" />';
                    } else {
                        $output = '<link rel="canonical" href="' . $base . '/" />';
                    }
                    
                } elseif ($art->startidartlang == $art->idartlang) {
			$output = '<link rel="canonical" href="' . $base . '{ref:idcat-' . $art->idcat . '}" />';
		} else {
			$output = '<link rel="canonical" href="' . $base . '{ref:idart-' . $art->idart . '}" />';
		}

		return $output;
	}

	protected function _cachingPeriod() {

		return 60 * 60 * 24 * 365;
	}

}