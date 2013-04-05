<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Image_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $defaults = array(
            'width' => 200,
            'height' => 200,
            'render' => 0,
            'template' => 'index',
            'idart' => Aitsu_Registry::get()->env->idart,
            'attr' => '',
            'all' => false
        );

        /* depracted */
        $defaults['style'] = '';
        $defaults['float'] = '';
        $defaults['rel'] = '';

        $morasoModuleConfig = Moraso_Config::get('module.image');

        foreach ($defaults as $key => $value) {
            $type = gettype($value);

            if (isset($morasoModuleConfig->$key->default)) {
                $default = $morasoModuleConfig->$key->default;
                $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
            }

            $defaults['configurable'][$key] = isset($morasoModuleConfig->$key->configurable) ? filter_var($morasoModuleConfig->$key->configurable, FILTER_VALIDATE_BOOLEAN) : false;

            if (isset($this->_params->default->$key)) {
                $default = $this->_params->default->$key;
                $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
            }

            if (isset($this->_params->$key)) {
                $default = $this->_params->$key;

                if ($default == 'config') {
                    $defaults['configurable'][$key] = true;
                } else {
                    $defaults[$key] = $type == 'integer' ? (int) $default : ($type == 'boolean' ? filter_var($default, FILTER_VALIDATE_BOOLEAN) : $default);
                }
            }
        }

        if (!empty($defaults['style'])) {
            trigger_error('Du benutzt in dem Image Modul den Parameter "style" welcher als "depracted" makiert ist. Nutze bitte den Parameter "attr.style".');
        }

        if (!empty($defaults['float'])) {
            trigger_error('Du benutzt in dem Image Modul den Parameter "float" welcher als "depracted" makiert ist. Nutze bitte den Parameter "attr.style.float".');
        }

        if (!empty($defaults['rel'])) {
            trigger_error('Du benutzt in dem Image Modul den Parameter "rel" welcher als "depracted" makiert ist. Nutze bitte den Parameter "attr.rel".');
        }

        return $defaults;
    }

    protected function _main() {

        $defaults = $this->_getDefaults();

        if ($defaults['configurable']['idart']) {
            $idart = Aitsu_Content_Config_Text::set($this->_index, 'idart', Aitsu_Translate::_('Article (idart)'), Aitsu_Translate::_('Configuration'));
        }

        $idart = !empty($idart) ? (int) $idart : $defaults['idart'];

        if ($defaults['configurable']['width']) {
            $width = Aitsu_Content_Config_Text::set($this->_index, 'width', Aitsu_Translate::_('Width'), Aitsu_Translate::_('Configuration'));
        }

        $width = !empty($width) ? (int) $width : $defaults['width'];

        if ($defaults['configurable']['height']) {
            $height = Aitsu_Content_Config_Text::set($this->_index, 'height', Aitsu_Translate::_('Height'), Aitsu_Translate::_('Configuration'));
        }

        $height = !empty($height) ? (int) $height : $defaults['height'];

        if ($defaults['configurable']['render']) {
            $renderSelect = array(
                'skalieren' => 0,
                'zuschneiden' => 1,
                'fokussieren' => 2
            );

            $render = Aitsu_Content_Config_Select::set($this->_index, 'render', Aitsu_Translate::_('Render'), $renderSelect, Aitsu_Translate::_('Configuration'));
        }

        $render = isset($render) && strlen($render) > 0 ? (int) $render : $defaults['render'];

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), Aitsu_Translate::_('Configuration'));
        }

        $template = !empty($template) ? $template : $defaults['template'];

        if ($defaults['configurable']['all']) {
            $showAllSelect = array(
                'show all Images' => true,
                'select single Images' => false
            );

            $all = Aitsu_Content_Config_Radio::set($this->_index, 'all', Aitsu_Translate::_('show all Images'), $showAllSelect, Aitsu_Translate::_('Configuration'));
        }

        $all = isset($all) && strlen($all) > 0 ? filter_var($all, FILTER_VALIDATE_BOOLEAN) : $defaults['all'];

        if (!$all) {
            $images = Moraso_Content_Config_Media :: set($this->_index, 'Image.Media', 'Media', $idart);
            $selectedImages = Moraso_Persistence_View_Media::byFileName($idart, $images);
        } else {
            $selectedImages = Moraso_Persistence_View_Media::ofSpecifiedArticle($idart);
        }

        if (!empty($defaults['attr'])) {
            $attr = new Zend_Config(Moraso_Util::object_to_array($defaults['attr']), array('allowModifications' => true));
        } else {
            $attr = new Zend_Config(array(), array('allowModifications' => true));
        }

        if ($defaults['configurable']['attr']) {
            $attr_config = Aitsu_Content_Config_Textarea::set($this->_index, 'attr', Aitsu_Translate::_('Attributes'), Aitsu_Translate::_('Configuration'));
        }

        if (!empty($attr_config)) {
            $config = new Zend_Config(Moraso_Util::parseSimpleIni($attr_config)->toArray(), array('allowModifications' => true));
            $attr = $attr->merge($config);
        }
        
        if ($defaults['configurable']['rel']) {
            $rel = Aitsu_Content_Config_Text::set($this->_index, 'rel', Aitsu_Translate::_('rel'), Aitsu_Translate::_('Configuration'));
        }

        $attr->rel = !empty($rel) ? $rel : (isset($defaults['attr']->rel) && !empty($defaults['attr']->rel)) ? $defaults['attr']->rel : $defaults['rel'];

        if (!isset($attr->style)) {
            $attr->style = new stdClass();
        }

        if ($defaults['configurable']['style']) {
            $attr->style->self = Aitsu_Content_Config_Text::set($this->_index, 'style', Aitsu_Translate::_('Style'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['float']) {
            $floatSelect = array(
                Aitsu_Translate::_('not specified') => '',
                Aitsu_Translate::_('left') => 'left',
                Aitsu_Translate::_('right') => 'right',
                Aitsu_Translate::_('none') => 'none'
            );

            $float = Aitsu_Content_Config_Select::set($this->_index, 'float', Aitsu_Translate::_('Float'), $floatSelect, Aitsu_Translate::_('Configuration'));
        }

        if (!empty($float)) {
            $attr->style->float = $float;
        } else {
            if (!isset($attr->style->float)) {
                if (isset($defaults['attr']->style->float) && !empty($defaults['attr']->style->float)) {
                    $attr->style->float = $defaults['attr']->style->float;
                } elseif (isset($defaults['float']) && !empty($defaults['float'])) { // depracted
                    $attr->style->float = $defaults['float'];
                }
            }
        }

        if (empty($template) || empty($selectedImages) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->selectedImages = $selectedImages;
        $view->width = $width;
        $view->height = $height;
        $view->render = $render;
        $view->attributes = $attr;

        /* depracted */
        if (isset($view->attributes->style->self)) {
            $view->style = $view->attributes->style->self;
        }

        if (isset($view->attributes->style->float)) {
            $view->float = $view->attributes->style->float;
        }

        if (isset($view->attributes->rel)) {
            $view->rel = $view->attributes->rel;
        }

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}