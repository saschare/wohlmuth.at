<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Articles_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();

        $categories = isset($this->_params->categories) ? $this->_params->categories : Aitsu_Registry::get()->env->idcat;
        $useOfStartArticle = isset($this->_params->useOfStartArticle) ? $this->_params->useOfStartArticle : 2;
        $sortCategoryFirst = isset($this->_params->sortCategoryFirst) ? $this->_params->sortCategoryFirst : false;
        $orderBy = isset($this->_params->orderBy) ? $this->_params->orderBy : 'artsort';
        $ascending = isset($this->_params->ascending) ? $this->_params->ascending : true;
        $template = isset($this->_params->template) ? $this->_params->template : 'index';
        $offset = isset($this->_params->offset) ? $this->_params->offset : 0;
        $limit = isset($this->_params->limit) ? $this->_params->limit : 10;
        $page = isset($this->_params->page) ? $this->_params->page : 0;

        if (!empty($page)) {
            $offset = ($page - 1) * $limit;
        }

        $aggregation = Moraso_Aggregation_Article::factory();
        $aggregation->useOfStartArticle($useOfStartArticle);
        $aggregation->whereInCategories(array($categories));

        if ($sortCategoryFirst) {
            $aggregation->orderBy('catlang.idcat');
        }

        $aggregation->orderBy($orderBy, $ascending);

        $aggregationAll = $aggregation;

        if (isset($this->_params->populateWith)) {
            foreach ($this->_params->populateWith as $alias => $populateWith) {

                $type = $populateWith->index;

                if ($populateWith->type == 'property' || $populateWith->type == 'files') {
                    $type = $populateWith->type . ':' . $type;
                }

                $aggregation->populateWith($type, $alias, $populateWith->datatype);
            }
        }

        $view->articles = $aggregation->fetch($offset, $limit);

        if (!empty($page)) {
            $articlesAll = $aggregationAll->fetch(0, 999);

            $view->pages = ceil(count($articlesAll) / $limit);
            $view->currentPage = $page;
        }

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return Aitsu_Util_Date::secondsUntilEndOf('day');
    }

}