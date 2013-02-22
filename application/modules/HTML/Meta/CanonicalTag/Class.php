<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
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
            if (Aitsu_Config::get('rewrite.uselang')) {
                $language = Aitsu_Persistence_Language::factory(Aitsu_Registry::get()->env->idlang)->name;
                $href = '/' . $language . '/';
            } else {
                $href = '/';
            }
        } elseif ($art->startidartlang == $art->idartlang) {
            $href = '{ref:idcat-' . $art->idcat . '}';
        } else {
            $href = '{ref:idart-' . $art->idart . '}';
        }

        return '<link rel="canonical" href="' . $base . '' . $href . '" />';
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}