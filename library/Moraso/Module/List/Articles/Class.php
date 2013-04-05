<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Articles_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $defaults = array(
            'categories' => '' . Aitsu_Registry::get()->env->idcat . '',
            'useOfStartArticle' => 2,
            'sortCategoryFirst' => false,
            'orderBy' => 'artsort',
            'ascending' => true,
            'template' => 'index',
            'offset' => 0,
            'limit' => 10,
            'page' => 1,
            'templateRenderingWhenNoArticles' => true
        );
        
        return $this->_getModulConfigDefaults($defaults, 'list.articles');
    }

    protected function _main() {

        $defaults = $this->_getDefaults();
        
        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        /* categories */
        if ($defaults['configurable']['categories']) {
            $categories = Aitsu_Content_Config_Text::set($this->_index, 'categories', Aitsu_Translate::_('Categories'), $translation['configuration']);
        }

        $categories = isset($categories) ? $categories : $defaults['categories'];

        /* useOfStartArticle */
        if ($defaults['configurable']['useOfStartArticle']) {
            $useOfStartArticleSelect = array(
                'show all articles' => 1,
                'do not show start articles' => 2,
                'show only start articles' => 3
            );

            $useOfStartArticle = Aitsu_Content_Config_Select::set($this->_index, 'useOfStartArticle', Aitsu_Translate::_('useOfStartArticle'), $useOfStartArticleSelect, $translation['configuration']);
        }

        $useOfStartArticle = isset($useOfStartArticle) ? (int) $useOfStartArticle : $defaults['useOfStartArticle'];

        /* sortCategoryFirst */
        if ($defaults['configurable']['sortCategoryFirst']) {
            $sortCategoryFirstSelect = array(
                'true' => true,
                'false' => false
            );

            $sortCategoryFirst = Aitsu_Content_Config_Select::set($this->_index, 'sortCategoryFirst', Aitsu_Translate::_('sortCategoryFirst'), $sortCategoryFirstSelect, $translation['configuration']);
        }

        $sortCategoryFirst = isset($sortCategoryFirst) && strlen($sortCategoryFirst) > 0 ? filter_var($sortCategoryFirst, FILTER_VALIDATE_BOOLEAN) : $defaults['sortCategoryFirst'];

        /* orderBy */
        if ($defaults['configurable']['orderBy']) {
            $orderBySelect = array(
                'artsort' => 'artsort',
                'created' => 'created',
                'modified' => 'modified',
                'metadate' => 'metadate'
            );

            $orderBy = Aitsu_Content_Config_Select::set($this->_index, 'orderBy', Aitsu_Translate::_('orderBy'), $orderBySelect, $translation['configuration']);
        }

        $orderBy = isset($orderBy) ? $orderBy : $defaults['orderBy'];

        /* ascending */
        if ($defaults['configurable']['ascending']) {
            $ascendingSelect = array(
                'true' => true,
                'false' => false
            );

            $ascending = Aitsu_Content_Config_Select::set($this->_index, 'ascending', Aitsu_Translate::_('ascending'), $ascendingSelect, $translation['configuration']);
        }

        $ascending = isset($ascending) && strlen($ascending) > 0 ? filter_var($ascending, FILTER_VALIDATE_BOOLEAN) : $defaults['ascending'];

        /* template */
        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = isset($template) ? $template : $defaults['template'];

        /* Offset */
        if ($defaults['configurable']['offset']) {
            $offset = Aitsu_Content_Config_Text::set($this->_index, 'offset', Aitsu_Translate::_('Offset'), $translation['configuration']);
        }

        $offset = isset($offset) ? (int) $offset : $defaults['offset'];

        /* Limit */
        if ($defaults['configurable']['limit']) {
            $limit = Aitsu_Content_Config_Text::set($this->_index, 'limit', Aitsu_Translate::_('Limit'), $translation['configuration']);
        }

        $limit = isset($limit) ? (int) $limit : $defaults['limit'];

        /* Page */
        if ($defaults['configurable']['page']) {
            $page = Aitsu_Content_Config_Text::set($this->_index, 'page', Aitsu_Translate::_('Page'), $translation['configuration']);
        }

        $page = isset($_GET['page']) ? (int) $_GET['page'] : (isset($page) ? (int) $page : $defaults['page']);

        /* templateRenderingWhenNoArticles */
        if ($defaults['configurable']['templateRenderingWhenNoArticles']) {
            $templateRenderingWhenNoArticlesSelect = array(
                'true' => true,
                'false' => false
            );

            $templateRenderingWhenNoArticles = Aitsu_Content_Config_Select::set($this->_index, 'templateRenderingWhenNoArticles', Aitsu_Translate::_('templateRenderingWhenNoArticles'), $templateRenderingWhenNoArticlesSelect, $translation['configuration']);
        }

        $templateRenderingWhenNoArticles = isset($templateRenderingWhenNoArticles) && strlen($templateRenderingWhenNoArticles) > 0 ? filter_var($templateRenderingWhenNoArticles, FILTER_VALIDATE_BOOLEAN) : $defaults['templateRenderingWhenNoArticles'];

        /* change Offset if not on first Page */
        if ($page > 1) {
            $offset = ($page - 1) * $limit;
        }

        $aggregation = Moraso_Aggregation_Article::factory();
        $aggregation->useOfStartArticle($useOfStartArticle);
        $aggregation->whereInCategories(array($categories));

        if ($sortCategoryFirst) {
            $aggregation->orderBy('catlang.idcat');
        }

        $aggregation->orderBy($orderBy, $ascending);

        if (isset($this->_params->populateWith)) {
            foreach ($this->_params->populateWith as $alias => $populateWith) {

                $type = $populateWith->index;

                if ($populateWith->type == 'property' || $populateWith->type == 'files') {
                    $type = $populateWith->type . ':' . $type;
                }

                $aggregation->populateWith($type, $alias, $populateWith->datatype);
            }
        }

        $aggregationAll = clone $aggregation;

        $articles = $aggregation->fetch($offset, $limit);

        if ((count($articles) === 0 && !$templateRenderingWhenNoArticles) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $articlesAll = $aggregationAll->fetch(0, 99999);

        $view = $this->_getView();

        $view->articles = $articles;
        $view->pages = ceil(count($articlesAll) / $limit);
        $view->currentPage = $page;
        $view->idart = Aitsu_Registry::get()->env->idart;

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return Aitsu_Util_Date::secondsUntilEndOf('day');
    }

}