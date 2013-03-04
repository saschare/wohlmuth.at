<?php

/**
 * @author Andreas Kummer <a.kummer@wdrei.ch>
 * @copyright (c) 2013, w3concepts AG 
 */
class Module_List_Rendezvous_Class extends Aitsu_Module_Tree_Abstract {

    protected $_cacheIfLoggedIn = true;
    protected $_isVolatile = true;

    protected function _main() {

        $view = $this->_getView();

        $view->dates = Aitsu_Persistence_View_Rendezvous :: getDates(
                        Aitsu_Util_Date :: dayOfCurrentWeek(1), Aitsu_Util_Date :: dayOfCurrentWeek(1)->add(60 * 60 * 24 * 7), Aitsu_Registry :: get()->env->idcat
        );

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 60 * 60 * 24 * 365;
    }

}