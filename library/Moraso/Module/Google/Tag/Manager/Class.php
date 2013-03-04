<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Tag_Manager_Class extends Moraso_Module_Abstract {

    protected $_cacheIfLoggedIn = true;
    protected $_allowEdit = false;

    protected function _main() {

        if (empty($this->_params->container)) {
            return '';
        }

        $view = $this->_getView();

        $view->container = $this->_params->container;

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}