<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_ResourceBundle_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;
    protected $_cacheIfLoggedIn = true;
    protected $_disableCacheArticleRelation = true;

    protected function _init() {

        $this->_idSuffix = Aitsu_Config :: get('skin');
    }

    protected function _main() {

        $type = $this->_params->type;

        $resources = array();
        foreach ($this->_params->res as $key => $resource) {
            $resources[] = $resource;
        }

        $uri = Aitsu_MiniMe :: getUri($type, $resources);

        $env = Aitsu_Config :: get('env') == null ? '' : Aitsu_Config :: get('env');

        if ($type == 'js') {
            $output = '<script type="text/javascript" src="' . $env . '/js/' . $uri . '"></script>';
        }

        if ($type == 'css') {
            if (isset($this->_params->media)) {
                $output = '<link type="text/css" rel="stylesheet" href="' . $env . '/css/' . $uri . '" media="' . $this->_params->media . '" /> ';
            } else {
                $output = '<link type="text/css" rel="stylesheet" href="' . $env . '/css/' . $uri . '" /> ';
            }
        }

        return $output;
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}