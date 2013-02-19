<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Image_Class extends Aitsu_Module_Abstract {

    protected function _getDefaults() {

        return array(
            'imageWidth' => 200,
            'imageHeight' => 200,
            'imageRender' => 0,
            'imageFloat' => '',
            'imageStyle' => '',
            'template' => 'index',
            'idart' => Aitsu_Registry :: get()->env->idart,
            'configurable' => array(
                'width' => true,
                'height' => true,
                'render' => true,
                'float' => true,
                'style' => true,
                'template' => true
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