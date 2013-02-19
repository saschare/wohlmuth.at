<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Image_Class extends Aitsu_Module_Abstract {

    protected function _getDefaults($params) {

        $imageWidth = empty($params->imageWidth) ? 200 : $params->imageWidth;
        $imageHeight = empty($params->imageHeight) ? 200 : $params->imageHeight;
        $imageRender = empty($params->imageRender) ? 0 : $params->imageRender;
        $imageFloat = empty($params->imageFloat) ? '' : $params->imageFloat;
        $imageStyle = empty($params->imageStyle) ? '' : $params->imageStyle;
        $template = empty($params->template) ? 'index' : $params->template;
        $idart = empty($params->idart) ? Aitsu_Registry :: get()->env->idart : $params->idart;
        
        $configurable = array();
        $configurable['width'] = (!isset($params->configurable->width) && empty($params->configurable->width)) ? false : ($params->configurable->width == 'true') ? true : false;
        $configurable['height'] = (!isset($params->configurable->height) && empty($params->configurable->height)) ? false : ($params->configurable->height == 'true') ? true : false;
        $configurable['render'] = (!isset($params->configurable->render) && empty($params->configurable->render)) ? false : ($params->configurable->render == 'true') ? true : false;
        $configurable['float'] = (!isset($params->configurable->float) && empty($params->configurable->float)) ? false : ($params->configurable->float == 'true') ? true : false;
        $configurable['style'] = (!isset($params->configurable->style) && empty($params->configurable->style)) ? false : ($params->configurable->style == 'true') ? true : false;
        $configurable['template'] = (!isset($params->configurable->template) && empty($params->configurable->template)) ? false : ($params->configurable->template == 'true') ? true : false;
        
        return array(
            'imageWidth' => $imageWidth,
            'imageHeight' => $imageHeight,
            'imageRender' => $imageRender,
            'imageFloat' => $imageFloat,
            'imageStyle' => $imageStyle,
            'template' => $template,
            'idart' => $idart,
            'configurable' => array(
                'width' => $configurable['width'],
                'height' => $configurable['height'],
                'render' => $configurable['render'],
                'float' => $configurable['float'],
                'style' => $configurable['style'],
                'template' => $configurable['template']
            )
        );
    }

    protected function _main() {

        $defaults = $this->_getDefaults($this->_params);

        $idart = empty($this->_params->idart) ? $defaults['idart'] : $this->_params->idart;
        $template = empty($this->_params->template) ? $defaults['template'] : $this->_params->template;

        $images = Moraso_Content_Config_Media :: set($this->_index, 'Image.Media', 'Media', $idart);
        $selectedImages = Aitsu_Persistence_View_Media :: byFileName($idart, $images);

        if ($defaults['configurable']['width']) {
            $imageWidth = Aitsu_Content_Config_Text::set($this->_index, 'width', Aitsu_Translate::_('Width'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['height']) {
            $imageHeight = Aitsu_Content_Config_Text::set($this->_index, 'height', Aitsu_Translate::_('Height'), Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['render']) {
            $render = array(
                'Variante 1' => 0,
                'Variante 2' => 1,
                'Variante 3' => 2
            );

            $imageRender = Aitsu_Content_Config_Select::set($this->_index, 'render', Aitsu_Translate::_('Render'), $render, Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['float']) {
            $float = array(
                Aitsu_Translate::_('not specified') => '',
                Aitsu_Translate::_('left') => 'left',
                Aitsu_Translate::_('right') => 'right',
                Aitsu_Translate::_('none') => 'none'
            );

            $imageFloat = Aitsu_Content_Config_Select::set($this->_index, 'float', Aitsu_Translate::_('Float'), $float, Aitsu_Translate::_('Configuration'));
        }

        if ($defaults['configurable']['style']) {
            $imageStyle = Aitsu_Content_Config_Text::set($this->_index, 'style', Aitsu_Translate::_('Style'), Aitsu_Translate::_('Configuration'));
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
        $view->imageWidth = empty($imageWidth) ? $defaults['imageWidth'] : $imageWidth;
        $view->imageHeight = empty($imageHeight) ? $defaults['imageHeight'] : $imageHeight;
        $view->imageRender = empty($imageRender) ? $defaults['imageRender'] : $imageRender;
        $view->imageFloat = empty($imageFloat) ? $defaults['imageFloat'] : $imageFloat;
        $view->imageStyle = empty($imageStyle) ? $defaults['imageStyle'] : $imageStyle;

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}