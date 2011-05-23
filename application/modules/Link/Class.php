<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011,webtischlerei
 */
class Module_Link_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {
        $instance = new self();

        Aitsu_Content_Edit::isBlock('Link', false);

        $index = empty($context['index']) ? 'noindex' : $context['index'];

        $name = Aitsu_Content_Config_Text::set($index, 'name', 'Name', 'Link');

        $link = Aitsu_Content_Config_Link::set($index, 'link', 'Link', 'Link');

        $targets = array(
            '_blank' => '_blank',
            '_top' => '_top',
            '_self' => '_self'
        );

        $target = Aitsu_Content_Config_Select::set($index, 'target', 'Target', $targets, 'Link');

        if (strpos($link, 'idcat') !== false || strpos($link, 'idart') !== false) {
            $link = str_replace(' ', '-', $link);
            $link = '{ref:' . $link . '}';
        }

        if (empty($link) && Aitsu_Registry::isEdit()) {
            $link = '<a href="#">no link given</a>';
        } else {
            $link = '<a href="' . $link . '" target="' . $target . '">' . $name . '</a>';
        }
        
        return $link;
    }

}