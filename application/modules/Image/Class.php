<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Image_Class extends Aitsu_Module_Abstract {

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
            'configurable' => array(
                'width' => Aitsu_Config::get('module.image.width.configurable'),
                'height' => Aitsu_Config::get('module.image.height.configurable'),
                'render' => Aitsu_Config::get('module.image.render.configurable'),
                'float' => Aitsu_Config::get('module.image.float.configurable'),
                'style' => Aitsu_Config::get('module.image.style.configurable'),
                'template' => Aitsu_Config::get('module.image.template.configurable'),
                'idart' => Aitsu_Config::get('module.image.idart.configurable'),
                'rel' => Aitsu_Config::get('module.image.rel.configurable')
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
            'configurable' => array(
                'width' => empty($aitsuConfig['configurable']['width']) ? false : $aitsuConfig['configurable']['width'],
                'height' => empty($aitsuConfig['configurable']['height']) ? false : $aitsuConfig['configurable']['height'],
                'render' => empty($aitsuConfig['configurable']['render']) ? false : $aitsuConfig['configurable']['render'],
                'float' => empty($aitsuConfig['configurable']['float']) ? false : $aitsuConfig['configurable']['float'],
                'style' => empty($aitsuConfig['configurable']['style']) ? false : $aitsuConfig['configurable']['style'],
                'template' => empty($aitsuConfig['configurable']['template']) ? false : $aitsuConfig['configurable']['template'],
                'rel' => empty($aitsuConfig['configurable']['rel']) ? false : $aitsuConfig['configurable']['rel']
            )
        );

        foreach ($this->_params->default as $param => $value) {
            $defaults[$param] = $value;
        }

        $width = empty($this->_params->width) || $this->_params->width == 'config' ? $defaults['width'] : $this->_params->width;
        $height = empty($this->_params->height) || $this->_params->height == 'config' ? $defaults['height'] : $this->_params->height;
        $render = empty($this->_params->render) || $this->_params->render == 'config' ? $defaults['render'] : $this->_params->render;
        $float = empty($this->_params->float) || $this->_params->float == 'config' ? $defaults['float'] : $this->_params->float;
        $style = empty($this->_params->style) || $this->_params->style == 'config' ? $defaults['style'] : $this->_params->style;
        $template = empty($this->_params->template) || $this->_params->template == 'config' ? $defaults['template'] : $this->_params->template;
        $idart = empty($this->_params->idart) || $this->_params->idart == 'config' ? $defaults['idart'] : $this->_params->idart;
        $rel = empty($this->_params->rel) || $this->_params->rel == 'config' ? $defaults['rel'] : $this->_params->rel;

        return array(
            'width' => $width,
            'height' => $height,
            'render' => $render,
            'float' => $float,
            'style' => $style,
            'template' => $template,
            'idart' => $idart,
            'rel' => $rel,
            'configurable' => array(
                'width' => $this->_params->width == 'config' ? true : $defaults['configurable']['width'],
                'height' => $this->_params->height == 'config' ? true : $defaults['configurable']['height'],
                'render' => $this->_params->render == 'config' ? true : $defaults['configurable']['render'],
                'float' => $this->_params->float == 'config' ? true : $defaults['configurable']['float'],
                'style' => $this->_params->style == 'config' ? true : $defaults['configurable']['style'],
                'template' => $this->_params->template == 'config' ? true : $defaults['configurable']['template'],
                'rel' => $this->_params->rel == 'config' ? true : $defaults['configurable']['rel']
            )
        );
    }

    protected function _main() {

        $defaults = $this->_getDefaults();

        $idart = empty($this->_params->idart) ? $defaults['idart'] : $this->_params->idart;
        $template = empty($this->_params->template) ? $defaults['template'] : $this->_params->template;

        $images = Moraso_Content_Config_Media :: set($this->_index, 'Image.Media', 'Media', $idart);
        $selectedImages = Aitsu_Persistence_View_Media :: byFileName($idart, $images);

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

        if ($defaults['configurable']['float']) {
            $floatSelect = array(
                Aitsu_Translate::_('not specified') => '',
                Aitsu_Translate::_('left') => 'left',
                Aitsu_Translate::_('right') => 'right',
                Aitsu_Translate::_('none') => 'none'
            );

            $float = Aitsu_Content_Config_Select::set($this->_index, 'float', Aitsu_Translate::_('Float'), $floatSelect, Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['style']) {
            $style = Aitsu_Content_Config_Text::set($this->_index, 'style', Aitsu_Translate::_('Style'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['template']) {
            $configTemplate = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), Aitsu_Translate::_('Configuration'));

            if (!empty($configTemplate)) {
                $template = $configTemplate;
            }
        }
        
        if ($defaults['configurable']['rel']) {
            $rel = Aitsu_Content_Config_Text::set($this->_index, 'rel', Aitsu_Translate::_('rel'), Aitsu_Translate::_('Configuration'));
        }

        if (empty($template) || empty($selectedImages) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->selectedImages = $selectedImages;
        $view->width = empty($width) ? $defaults['width'] : $width;
        $view->height = empty($height) ? $defaults['height'] : $height;
        $view->render = empty($render) ? $defaults['render'] : $render;
        $view->float = empty($float) ? $defaults['float'] : $float;
        $view->style = empty($style) ? $defaults['style'] : $style;
        $view->rel = empty($rel) ? $defaults['rel'] : $rel;

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}