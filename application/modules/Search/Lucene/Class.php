<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Search_Lucene_Class extends Aitsu_Module_Abstract {

    protected $_allowEdit = false;

    protected function _init() {

        Aitsu_Registry::setExpireTime(0);

        $searchterm = $_REQUEST['searchterm'];

        $search_area = Aitsu_Config::get('search.lucene.area');

        $search_array = array();
        foreach ($search_area as $idcat) {
            $search_array[] = $idcat;
        }

        $view = $this->_getView();
        $view->searchterm = $searchterm;

        try {
            $view->results = Aitsu_Lucene_Index::find($searchterm, $search_array);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            $view->results = array();
        }

        return $view->render('index.phtml');
    }

}