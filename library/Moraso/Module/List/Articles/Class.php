<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Articles_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $aitsuConfig = array(
            'categories' => Aitsu_Config::get('module.list.articles.categories.default'),
            'useOfStartArticle' => Aitsu_Config::get('module.list.articles.useOfStartArticle.default'),
            'sortCategoryFirst' => Aitsu_Config::get('module.list.articles.sortCategoryFirst.default'),
            'orderBy' => Aitsu_Config::get('module.list.articles.orderBy.default'),
            'ascending' => Aitsu_Config::get('module.list.articles.ascending.default'),
            'template' => Aitsu_Config::get('module.list.articles.template.default'),
            'offset' => Aitsu_Config::get('module.list.articles.offset.default'),
            'limit' => Aitsu_Config::get('module.list.articles.limit.default'),
            'page' => Aitsu_Config::get('module.list.articles.page.default'),
            'templateRenderingWhenNoArticles' => Aitsu_Config::get('module.list.articles.page.templateRenderingWhenNoArticles'),
            'configurable' => array(
                'categories' => Aitsu_Config::get('module.list.articles.categories.configurable'),
                'useOfStartArticle' => Aitsu_Config::get('module.list.articles.useOfStartArticle.configurable'),
                'sortCategoryFirst' => Aitsu_Config::get('module.list.articles.sortCategoryFirst.configurable'),
                'orderBy' => Aitsu_Config::get('module.list.articles.orderBy.configurable'),
                'ascending' => Aitsu_Config::get('module.list.articles.ascending.configurable'),
                'template' => Aitsu_Config::get('module.list.articles.template.configurable'),
                'offset' => Aitsu_Config::get('module.list.articles.offset.configurable'),
                'limit' => Aitsu_Config::get('module.list.articles.limit.configurable'),
                'page' => Aitsu_Config::get('module.list.articles.page.configurable'),
                'templateRenderingWhenNoArticles' => Aitsu_Config::get('module.list.articles.templateRenderingWhenNoArticles.configurable')
            )
        );

        $defaults = array(
            'categories' => empty($aitsuConfig['categories']) ? Aitsu_Registry::get()->env->idcat : $aitsuConfig['categories'],
            'useOfStartArticle' => empty($aitsuConfig['useOfStartArticle']) ? 2 : $aitsuConfig['useOfStartArticle'],
            'sortCategoryFirst' => empty($aitsuConfig['sortCategoryFirst']) ? false : $aitsuConfig['sortCategoryFirst'],
            'orderBy' => empty($aitsuConfig['orderBy']) ? 'artsort' : $aitsuConfig['orderBy'],
            'ascending' => empty($aitsuConfig['ascending']) ? true : $aitsuConfig['ascending'],
            'template' => empty($aitsuConfig['template']) ? 'index' : $aitsuConfig['template'],
            'offset' => empty($aitsuConfig['offset']) ? 0 : $aitsuConfig['offset'],
            'limit' => empty($aitsuConfig['limit']) ? 10 : $aitsuConfig['limit'],
            'page' => empty($aitsuConfig['page']) ? 0 : $aitsuConfig['page'],
            'templateRenderingWhenNoArticles' => empty($aitsuConfig['templateRenderingWhenNoArticles']) ? true : $aitsuConfig['templateRenderingWhenNoArticles'],
            'configurable' => array(
                'categories' => empty($aitsuConfig['configurable']['categories']) ? false : $aitsuConfig['configurable']['categories'],
                'useOfStartArticle' => empty($aitsuConfig['configurable']['useOfStartArticle']) ? false : $aitsuConfig['configurable']['useOfStartArticle'],
                'sortCategoryFirst' => empty($aitsuConfig['configurable']['sortCategoryFirst']) ? false : $aitsuConfig['configurable']['sortCategoryFirst'],
                'orderBy' => empty($aitsuConfig['configurable']['orderBy']) ? false : $aitsuConfig['configurable']['orderBy'],
                'ascending' => empty($aitsuConfig['configurable']['ascending']) ? false : $aitsuConfig['configurable']['ascending'],
                'template' => empty($aitsuConfig['configurable']['template']) ? false : $aitsuConfig['configurable']['template'],
                'offset' => empty($aitsuConfig['configurable']['offset']) ? false : $aitsuConfig['configurable']['offset'],
                'limit' => empty($aitsuConfig['configurable']['limit']) ? false : $aitsuConfig['configurable']['limit'],
                'page' => empty($aitsuConfig['configurable']['page']) ? false : $aitsuConfig['configurable']['page'],
                'templateRenderingWhenNoArticles' => empty($aitsuConfig['configurable']['templateRenderingWhenNoArticles']) ? false : $aitsuConfig['configurable']['templateRenderingWhenNoArticles']
            )
        );

        if (isset($this->_params->default)) {
            foreach ($this->_params->default as $param => $value) {
                $defaults[$param] = $value;
            }
        }

        $categories = empty($this->_params->categories) || $this->_params->categories == 'config' ? $defaults['categories'] : $this->_params->categories;
        $useOfStartArticle = empty($this->_params->useOfStartArticle) || $this->_params->useOfStartArticle == 'config' ? $defaults['useOfStartArticle'] : $this->_params->useOfStartArticle;
        $sortCategoryFirst = empty($this->_params->sortCategoryFirst) || $this->_params->sortCategoryFirst == 'config' ? $defaults['sortCategoryFirst'] : $this->_params->sortCategoryFirst;
        $orderBy = empty($this->_params->orderBy) || $this->_params->orderBy == 'config' ? $defaults['orderBy'] : $this->_params->orderBy;
        $ascending = empty($this->_params->ascending) || $this->_params->ascending == 'config' ? $defaults['ascending'] : $this->_params->ascending;
        $template = empty($this->_params->template) || $this->_params->template == 'config' ? $defaults['template'] : $this->_params->template;
        $offset = empty($this->_params->offset) || $this->_params->offset == 'config' ? $defaults['offset'] : $this->_params->offset;
        $limit = empty($this->_params->limit) || $this->_params->limit == 'config' ? $defaults['limit'] : $this->_params->limit;
        $page = empty($this->_params->page) || $this->_params->page == 'config' ? $defaults['page'] : $this->_params->page;
        $templateRenderingWhenNoArticles = empty($this->_params->templateRenderingWhenNoArticles) || $this->_params->templateRenderingWhenNoArticles == 'config' ? $defaults['templateRenderingWhenNoArticles'] : $this->_params->templateRenderingWhenNoArticles;

        return array(
            'categories' => $categories,
            'useOfStartArticle' => $useOfStartArticle,
            'sortCategoryFirst' => $sortCategoryFirst,
            'orderBy' => $orderBy,
            'ascending' => $ascending,
            'template' => $template,
            'offset' => $offset,
            'limit' => $limit,
            'page' => $page,
            'templateRenderingWhenNoArticles' => $templateRenderingWhenNoArticles,
            'configurable' => array(
                'categories' => isset($this->_params->categories) && $this->_params->categories == 'config' ? true : $defaults['configurable']['categories'],
                'useOfStartArticle' => isset($this->_params->useOfStartArticle) && $this->_params->useOfStartArticle == 'config' ? true : $defaults['configurable']['useOfStartArticle'],
                'sortCategoryFirst' => isset($this->_params->sortCategoryFirst) && $this->_params->sortCategoryFirst == 'config' ? true : $defaults['configurable']['sortCategoryFirst'],
                'orderBy' => isset($this->_params->orderBy) && $this->_params->orderBy == 'config' ? true : $defaults['configurable']['orderBy'],
                'ascending' => isset($this->_params->ascending) && $this->_params->ascending == 'config' ? true : $defaults['configurable']['ascending'],
                'template' => isset($this->_params->template) && $this->_params->template == 'config' ? true : $defaults['configurable']['template'],
                'offset' => isset($this->_params->offset) && $this->_params->offset == 'config' ? true : $defaults['configurable']['offset'],
                'limit' => isset($this->_params->limit) && $this->_params->limit == 'config' ? true : $defaults['configurable']['limit'],
                'page' => isset($this->_params->page) && $this->_params->page == 'config' ? true : $defaults['configurable']['page'],
                'templateRenderingWhenNoArticles' => isset($this->_params->templateRenderingWhenNoArticles) && $this->_params->templateRenderingWhenNoArticles == 'config' ? true : $defaults['configurable']['templateRenderingWhenNoArticles']
            )
        );
    }

    protected function _main() {

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        $defaults = $this->_getDefaults();

        /* categories */
        if ($defaults['configurable']['categories']) {
            $categories = Aitsu_Content_Config_Text::set($this->_index, 'categories', Aitsu_Translate::_('Categories'), $translation['configuration']);
        }

        if (empty($categories)) {
            $categories = $defaults['categories'];
        }

        /* useOfStartArticle */
        if ($defaults['configurable']['useOfStartArticle']) {
            $useOfStartArticleSelect = array(
                'show all articles' => 1,
                'do not show start articles' => 2,
                'show only start articles' => 3
            );

            $useOfStartArticle = Aitsu_Content_Config_Select::set($this->_index, 'useOfStartArticle', Aitsu_Translate::_('useOfStartArticle'), $useOfStartArticleSelect, $translation['configuration']);
        }

        if (empty($useOfStartArticle)) {
            $useOfStartArticle = $defaults['useOfStartArticle'];
        }

        /* sortCategoryFirst */
        if ($defaults['configurable']['sortCategoryFirst']) {
            $sortCategoryFirstSelect = array(
                'true' => 'true',
                'false' => 'false'
            );

            $sortCategoryFirst = Aitsu_Content_Config_Select::set($this->_index, 'sortCategoryFirst', Aitsu_Translate::_('sortCategoryFirst'), $sortCategoryFirstSelect, $translation['configuration']);
        }

        if ($sortCategoryFirst === 'true') {
            $sortCategoryFirst = true;
        } elseif ($sortCategoryFirst === 'false') {
            $sortCategoryFirst = false;
        } else {
            $sortCategoryFirst = $defaults['sortCategoryFirst'];
        }

        /* orderBy */
        if ($defaults['configurable']['orderBy']) {
            $orderBySelect = array(
                'artsort' => 'artsort',
                'created' => 'created'
            );

            $orderBy = Aitsu_Content_Config_Select::set($this->_index, 'orderBy', Aitsu_Translate::_('orderBy'), $orderBySelect, $translation['configuration']);
        }

        if (empty($orderBy)) {
            $orderBy = $defaults['orderBy'];
        }

        /* ascending */
        if ($defaults['configurable']['ascending']) {
            $ascendingSelect = array(
                'true' => 'true',
                'false' => 'false'
            );

            $ascending = Aitsu_Content_Config_Select::set($this->_index, 'ascending', Aitsu_Translate::_('ascending'), $ascendingSelect, $translation['configuration']);
        }

        if ($ascending === 'true') {
            $ascending = true;
        } elseif ($ascending === 'false') {
            $ascending = false;
        } else {
            $ascending = $defaults['ascending'];
        }

        /* template */
        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        if (empty($template)) {
            $template = $defaults['template'];
        }

        /* Offset */
        if ($defaults['configurable']['offset']) {
            $offset = Aitsu_Content_Config_Text::set($this->_index, 'offset', Aitsu_Translate::_('Offset'), $translation['configuration']);
        }

        if (empty($offset)) {
            $offset = $defaults['offset'];
        }

        /* Limit */
        if ($defaults['configurable']['limit']) {
            $limit = Aitsu_Content_Config_Text::set($this->_index, 'limit', Aitsu_Translate::_('Limit'), $translation['configuration']);
        }

        if (empty($limit)) {
            $limit = $defaults['limit'];
        }

        /* Page */
        if ($defaults['configurable']['page']) {
            $page = Aitsu_Content_Config_Text::set($this->_index, 'page', Aitsu_Translate::_('Page'), $translation['configuration']);
        }

        if (empty($page)) {
            $page = $defaults['page'];
        }

        /* templateRenderingWhenNoArticles */
        if ($defaults['configurable']['templateRenderingWhenNoArticles']) {
            $templateRenderingWhenNoArticlesSelect = array(
                'true' => 'true',
                'false' => 'false'
            );

            $templateRenderingWhenNoArticles = Aitsu_Content_Config_Select::set($this->_index, 'templateRenderingWhenNoArticles', Aitsu_Translate::_('templateRenderingWhenNoArticles'), $templateRenderingWhenNoArticlesSelect, $translation['configuration']);
        }

        if ($templateRenderingWhenNoArticles === 'true') {
            $templateRenderingWhenNoArticles = true;
        } elseif ($templateRenderingWhenNoArticles === 'false') {
            $templateRenderingWhenNoArticles = false;
        } else {
            $templateRenderingWhenNoArticles = $defaults['templateRenderingWhenNoArticles'];
        }

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

        $articles = $aggregation->fetch($offset, $limit);

        if ((empty($articles) && !$templateRenderingWhenNoArticles) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->articles = $articles;

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