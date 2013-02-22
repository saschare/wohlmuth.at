<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Image_Class extends Aitsu_Module_Abstract {

    protected function _getDefaults() {
        
        $widthConfig = Aitsu_Config::get('module.image.width.default');
        
        $defaults = array(
            'width' => empty($widthConfig) ? 200 : $widthConfig,
            'height' => empty(Aitsu_Config::get('module.image.height.default')) ? 200 : Aitsu_Config::get('module.image.height.default'),
            'render' => empty(Aitsu_Config::get('module.image.render.default')) ? 0 : Aitsu_Config::get('module.image.render.default'),
            'float' => empty(Aitsu_Config::get('module.image.float.default')) ? '' : Aitsu_Config::get('module.image.float.default'),
            'style' => empty(Aitsu_Config::get('module.image.style.default')) ? '' : Aitsu_Config::get('module.image.style.default'),
            'template' => empty(Aitsu_Config::get('module.image.template.default')) ? 'index' : Aitsu_Config::get('module.image.template.default'),
            'idart' => empty(Aitsu_Config::get('module.image.idart.default')) ? Aitsu_Registry :: get()->env->idart : Aitsu_Config::get('module.image.idart.default'),
            'configurable' => array(
                'width' => empty(Aitsu_Config::get('module.image.width.configurable')) ? false : Aitsu_Config::get('module.image.width.configurable'),
                'height' => empty(Aitsu_Config::get('module.image.height.configurable')) ? false : Aitsu_Config::get('module.image.height.configurable'),
                'render' => empty(Aitsu_Config::get('module.image.render.configurable')) ? false : Aitsu_Config::get('module.image.render.configurable'),
                'float' => empty(Aitsu_Config::get('module.image.float.configurable')) ? false : Aitsu_Config::get('module.image.float.configurable'),
                'style' => empty(Aitsu_Config::get('module.image.style.configurable')) ? false : Aitsu_Config::get('module.image.style.configurable'),
                'template' => empty(Aitsu_Config::get('module.image.template.configurable')) ? false : Aitsu_Config::get('module.image.template.configurable')
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

        return array(
            'width' => $width,
            'height' => $height,
            'render' => $render,
            'float' => $float,
            'style' => $style,
            'template' => $template,
            'idart' => $idart,
            'configurable' => array(
                'width' => $this->_params->width == 'config' ? true : $defaults['configurable']['width'],
                'height' => $this->_params->height == 'config' ? true : $defaults['configurable']['height'],
                'render' => $this->_params->render == 'config' ? true : $defaults['configurable']['render'],
                'float' => $this->_params->float == 'config' ? true : $defaults['configurable']['float'],
                'style' => $this->_params->style == 'config' ? true : $defaults['configurable']['style'],
                'template' => $this->_params->template == 'config' ? true : $defaults['configurable']['template']
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

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}