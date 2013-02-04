<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Aitsu_Content_Config_Media extends Aitsu_Content_Config_Abstract {

    public function getTemplate() {

        return 'media_moraso.phtml';
    }

    public static function set($index, $name, $label) {

        $instance = new self($index, $name);

        $instance->facts['tab'] = true;
        $instance->facts['label'] = $label;
        $instance->facts['type'] = 'serialized';

        $instance->params['media'] = Moraso_Persistence_View_Media :: ofCurrentArticleWithoutDescription();

        return $instance->currentValue();
    }

}