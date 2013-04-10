<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Html_Helper_Navigation_Foundation_Topbar{

    public static function getHtml(array $nav, $ulId = null, array $ulClasses = array()) {

        $self = new self();

        return $self->_createUl($nav, $ulId, $ulClasses);
    }

    private function _createUl($nav, $ulId, $ulClasses) {
        
        $ul = '<ul';

        if (!empty($ulId)) {
            $ul.= ' id="' . $ulId . '"';
        }
        
        if (!empty($ulClasses)) {
            $ul.= ' class="' . implode(' ', $ulClasses) . '"';
        }

        $ul.= '>';

        foreach ($nav as $row) {
            $ul.= $this->_createLi($row);
        }

        $ul.= '</ul>';

        return $ul;
    }

    private function _createLi($row) {

        $liClasses = array();

        if ($row['isCurrent'] || $row['isParent']) {
            $liClasses[] = 'active';
        }

        if ($row['hasChildren']) {
            $liClasses[] = 'has-dropdown';
        }

        $li = '<li';

        if (!empty($liClasses)) {
            $li.= ' class="' . implode(' ', $liClasses) . '"';
        }

        $li.= '>';

        $li.= '<a href="{ref:idcat-' . $row['idcat'] . '}">' . $row['name'] . '</a>';

        if ($row['hasChildren']) {
            $li.= $this->_createUl($row['children'], null, array('dropdown'));
        }

        $li.= '</li>';

        return $li;
    }

}