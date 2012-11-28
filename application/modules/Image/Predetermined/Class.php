<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Image_Predetermined_Class extends Aitsu_Module_Abstract {

    protected function _main() {

        $images = Moraso_Content_Config_Media::set($this->_index, 'Image.Media', 'Media', $this->_params->idart);

        $view = $this->_getView();
        $view->images = Aitsu_Persistence_View_Media::byFileName($this->_params->idart, $images);

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}