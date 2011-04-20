<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Module_Search_Lucene_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {

        Aitsu_Content_Edit::noEdit('Search.Lucene', true);

        Aitsu_Registry::setExpireTime(0);

        $instance = new self();

        $searchterm = $_POST['searchterm'];

        $view = $instance->_getView();

        $view->searchterm = $searchterm;

        $search_area = Aitsu_Config::get('search.lucene.area');

        $search_array = array();
        foreach ($search_area as $idcat) {
            $search_array[] = $idcat;
        }

        try {
            $view->results = Aitsu_Lucene_Index::find($searchterm, $search_array);
        } catch (Exception $e) {
            $view->results = array();
        }

        $output = $view->render('index.phtml');

        return $output;
    }

}