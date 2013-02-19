<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Content_Config_Media extends Aitsu_Content_Config_Media {

    public function getTemplate() {

        return 'media_moraso.phtml';
    }

    public static function set($index, $name, $label, $idart = null) {

        if (empty($idart)) {
            $idart = Aitsu_Registry :: get()->env->idart;
        }

        $instance = new self($index, $name);

        $instance->facts['tab'] = true;
        $instance->facts['label'] = $label;
        $instance->facts['type'] = 'serialized';

        $instance->params['media'] = Moraso_Persistence_View_Media::ofSpecifiedArticle($idart);

        return $instance->currentValue();
    }

}