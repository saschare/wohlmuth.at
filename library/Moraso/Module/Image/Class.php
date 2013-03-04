<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Image_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $aitsuConfig = array(
            'width' => Aitsu_Config::get('module.image.width.default'),
            'height' => Aitsu_Config::get('module.image.height.default'),
            'render' => Aitsu_Config::get('module.image.render.default'),
            'float' => Aitsu_Config::get('module.image.float.default'),
            'style' => Aitsu_Config::get('module.image.style.default'),
            'template' => Aitsu_Config::get('module.image.template.default'),
            'idart' => Aitsu_Config::get('module.image.idart.default'),
            'rel' => Aitsu_Config::get('module.image.rel.default'),
            'attr' => Aitsu_Config::get('module.image.attr.default'),
            'all' => Aitsu_Config::get('module.image.all.default'),
            'configurable' => array(
                'width' => Aitsu_Config::get('module.image.width.configurable'),
                'height' => Aitsu_Config::get('module.image.height.configurable'),
                'render' => Aitsu_Config::get('module.image.render.configurable'),
                'float' => Aitsu_Config::get('module.image.float.configurable'),
                'style' => Aitsu_Config::get('module.image.style.configurable'),
                'template' => Aitsu_Config::get('module.image.template.configurable'),
                'idart' => Aitsu_Config::get('module.image.idart.configurable'),
                'rel' => Aitsu_Config::get('module.image.rel.configurable'),
                'attr' => Aitsu_Config::get('module.image.attr.configurable'),
                'all' => Aitsu_Config::get('module.image.all.configurable')
            )
        );

        $defaults = array(
            'width' => empty($aitsuConfig['width']) ? 200 : $aitsuConfig['width'],
            'height' => empty($aitsuConfig['height']) ? 200 : $aitsuConfig['height'],
            'render' => empty($aitsuConfig['render']) ? 0 : $aitsuConfig['render'],
            'float' => empty($aitsuConfig['float']) ? '' : $aitsuConfig['float'],
            'style' => empty($aitsuConfig['style']) ? '' : $aitsuConfig['style'],
            'template' => empty($aitsuConfig['template']) ? 'index' : $aitsuConfig['template'],
            'idart' => empty($aitsuConfig['idart']) ? Aitsu_Registry :: get()->env->idart : $aitsuConfig['idart'],
            'rel' => empty($aitsuConfig['rel']) ? '' : $aitsuConfig['rel'],
            'attr' => empty($aitsuConfig['attr']) ? '' : $aitsuConfig['attr'],
            'all' => empty($aitsuConfig['all']) ? 0 : $aitsuConfig['all'],
            'configurable' => array(
                'width' => empty($aitsuConfig['configurable']['width']) ? false : $aitsuConfig['configurable']['width'],
                'height' => empty($aitsuConfig['configurable']['height']) ? false : $aitsuConfig['configurable']['height'],
                'render' => empty($aitsuConfig['configurable']['render']) ? false : $aitsuConfig['configurable']['render'],
                'float' => empty($aitsuConfig['configurable']['float']) ? false : $aitsuConfig['configurable']['float'],
                'style' => empty($aitsuConfig['configurable']['style']) ? false : $aitsuConfig['configurable']['style'],
                'template' => empty($aitsuConfig['configurable']['template']) ? false : $aitsuConfig['configurable']['template'],
                'rel' => empty($aitsuConfig['configurable']['rel']) ? false : $aitsuConfig['configurable']['rel'],
                'attr' => empty($aitsuConfig['configurable']['attr']) ? false : $aitsuConfig['configurable']['attr'],
                'all' => empty($aitsuConfig['configurable']['all']) ? false : $aitsuConfig['configurable']['all']
            )
        );

        if (isset($this->_params->default)) {
            foreach ($this->_params->default as $param => $value) {
                $defaults[$param] = $value;
            }
        }

        $width = empty($this->_params->width) || $this->_params->width == 'config' ? $defaults['width'] : $this->_params->width;
        $height = empty($this->_params->height) || $this->_params->height == 'config' ? $defaults['height'] : $this->_params->height;
        $render = empty($this->_params->render) || $this->_params->render == 'config' ? $defaults['render'] : $this->_params->render;
        $float = empty($this->_params->float) || $this->_params->float == 'config' ? $defaults['float'] : $this->_params->float;
        $style = empty($this->_params->style) || $this->_params->style == 'config' ? $defaults['style'] : $this->_params->style;
        $template = empty($this->_params->template) || $this->_params->template == 'config' ? $defaults['template'] : $this->_params->template;
        $idart = empty($this->_params->idart) || $this->_params->idart == 'config' ? $defaults['idart'] : $this->_params->idart;
        $rel = empty($this->_params->rel) || $this->_params->rel == 'config' ? $defaults['rel'] : $this->_params->rel;
        $attr = empty($this->_params->attr) || $this->_params->attr == 'config' ? $defaults['attr'] : $this->_params->attr;
        $all = empty($this->_params->all) || $this->_params->all == 'config' ? $defaults['all'] : $this->_params->all;

        return array(
            'width' => $width,
            'height' => $height,
            'render' => $render,
            'float' => $float,
            'style' => $style,
            'template' => $template,
            'idart' => $idart,
            'rel' => $rel,
            'attr' => $attr,
            'all' => $all,
            'configurable' => array(
                'width' => isset($this->_params->width) && $this->_params->width == 'config' ? true : $defaults['configurable']['width'],
                'height' => isset($this->_params->height) && $this->_params->height == 'config' ? true : $defaults['configurable']['height'],
                'render' => isset($this->_params->render) && $this->_params->render == 'config' ? true : $defaults['configurable']['render'],
                'float' => isset($this->_params->float) && $this->_params->float == 'config' ? true : $defaults['configurable']['float'],
                'style' => isset($this->_params->style) && $this->_params->style == 'config' ? true : $defaults['configurable']['style'],
                'template' => isset($this->_params->template) && $this->_params->template == 'config' ? true : $defaults['configurable']['template'],
                'rel' => isset($this->_params->rel) && $this->_params->rel == 'config' ? true : $defaults['configurable']['rel'],
                'attr' => isset($this->_params->attr) && $this->_params->attr == 'config' ? true : $defaults['configurable']['attr'],
                'all' => isset($this->_params->all) && $this->_params->all == 'config' ? true : $defaults['configurable']['all']
            )
        );
    }

    protected function _main() {

        $defaults = $this->_getDefaults();

        $idart = empty($this->_params->idart) ? $defaults['idart'] : $this->_params->idart;
        $template = empty($this->_params->template) ? $defaults['template'] : $this->_params->template;

        if ($defaults['configurable']['width']) {
            $width = Aitsu_Content_Config_Text::set($this->_index, 'width', Aitsu_Translate::_('Width'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['height']) {
            $height = Aitsu_Content_Config_Text::set($this->_index, 'height', Aitsu_Translate::_('Height'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['render']) {
            $renderSelect = array(
                'Variante 1' => 0,
                'Variante 2' => 1,
                'Variante 3' => 2
            );

            $render = Aitsu_Content_Config_Select::set($this->_index, 'render', Aitsu_Translate::_('Render'), $renderSelect, Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['template']) {
            $configTemplate = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), Aitsu_Translate::_('Configuration'));

            if (!empty($configTemplate)) {
                $template = $configTemplate;
            }
        }

        if ($defaults['configurable']['all']) {
            $showAllSelect = array(
                'show all Images' => true,
                'select single Images' => false
            );

            $all = Aitsu_Content_Config_Radio::set($this->_index, 'all', Aitsu_Translate::_('show all Images'), $showAllSelect, Aitsu_Translate::_('Configuration'));
        }

        if (empty($all) && empty($defaults['all'])) {
            $images = Moraso_Content_Config_Media :: set($this->_index, 'Image.Media', 'Media', $idart);
            $selectedImages = Moraso_Persistence_View_Media::byFileName($idart, $images);
        } else {
            $selectedImages = Moraso_Persistence_View_Media::ofSpecifiedArticle($idart);
        }

        $attributes = $defaults['attr'];

        if ($defaults['configurable']['rel']) {
            $attributes->rel = Aitsu_Content_Config_Text::set($this->_index, 'rel', Aitsu_Translate::_('rel'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['style']) {
            $attributes->style->self = Aitsu_Content_Config_Text::set($this->_index, 'style', Aitsu_Translate::_('Style'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['float']) {
            $floatSelect = array(
                Aitsu_Translate::_('not specified') => '',
                Aitsu_Translate::_('left') => 'left',
                Aitsu_Translate::_('right') => 'right',
                Aitsu_Translate::_('none') => 'none'
            );

            $attributes->style->float = Aitsu_Content_Config_Select::set($this->_index, 'float', Aitsu_Translate::_('Float'), $floatSelect, Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['attr']) {
            $attributes_as_string = Aitsu_Content_Config_Textarea::set($this->_index, 'attr', Aitsu_Translate::_('Attributes'), Aitsu_Translate::_('Configuration'));

            $attributes_as_object = Aitsu_Util::parseSimpleIni($attributes_as_string);

            $attributes = (object) array_merge((array) $attributes, (array) $attributes_as_object);
        }

        /**
         * verkürzte Schreibweise für rel, style und float zulassen
         */
        if (!empty($defaults['rel']) && empty($attributes->rel)) {
            $attributes->rel = $defaults['rel'];
        }

        if (!empty($defaults['style']) && empty($attributes->style)) {
            $attributes->style = $defaults['style'];
        }

        if (!empty($defaults['float']) && empty($attributes->style->float)) {
            $attributes->style->float = $defaults['float'];
        }

        if (empty($template) || empty($selectedImages) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->selectedImages = $selectedImages;

        $view->width = empty($width) ? $defaults['width'] : $width;
        $view->height = empty($height) ? $defaults['height'] : $height;
        $view->render = empty($render) ? $defaults['render'] : $render;
        $view->attributes = array_merge((array) $defaults['attr'], (array) $attributes);

        /**
         * Diese 3 (float, style, rel) befinden sich in der $view->attributes
         * Habe es nur aus Kompatibilitätsgründen drin gelassen!
         */
        $view->float = empty($attributes->style->float) ? $defaults['float'] : $attributes->style->float; // depracted
        $view->style = empty($attributes->style->self) ? $defaults['style'] : $attributes->style->self; // depracted
        $view->rel = empty($attributes->rel) ? $defaults['rel'] : $attributes->rel; // depracted

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}