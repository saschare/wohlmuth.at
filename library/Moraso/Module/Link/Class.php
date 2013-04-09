<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Link_Class extends Moraso_Module_Abstract {

    protected $_isBlock = false;

    protected function _getDefaults() {

        $defaults = array(
            'target' => '_blank',
            'template' => 'index',
            'configurable' => array(
                'target' => true
            )
        );

        return $defaults;
    }

    protected function _main() {

        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];

        if ($defaults['configurable']['target']) {
            $targetSelect = array(
                '_blank' => '_blank',
                '_top' => '_top',
                '_self' => '_self',
                '_parent' => '_parent'
            );

            $target = Aitsu_Content_Config_Select::set($this->_index, 'orderBy', Aitsu_Translate::_('Target'), $targetSelect, $translation['configuration']);
        }

        $target = !empty($target) ? $target : $defaults['target'];

        $view = $this->_getView();

        $view->name = Aitsu_Content_Config_Text :: set($this->_index, 'name', 'Name', 'Link');
        $view->link = Aitsu_Content_Config_Link :: set($this->_index, 'link', 'Link', 'Link');
        $view->target = $target;

        if (strpos($view->link, 'idcat') !== false || strpos($view->link, 'idart') !== false) {
            $view->link = '{ref:' . str_replace(' ', '-', $view->link) . '}';
        }

        if (empty($view->link) || empty($view->name) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}