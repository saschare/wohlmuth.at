<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_Class extends Moraso_Module_Abstract {

    protected $type = 'navigation';
    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();
        
        $template = isset($this->_params->template) ? $this->_params->template : 'index';
        
        if (isset($this->_params->idcat) && !empty($this->_params->idcat)) {
            $idcat = $this->_params->idcat;
        } else {
            $idcat = Moraso_Config::get('navigation.' . $this->_index);
        }
        
        $view->nav = Moraso_Navigation_Frontend::getTree($idcat);

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}