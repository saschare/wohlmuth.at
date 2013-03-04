<?php

/**
 * @author Christian Kehres, <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Shortcodes_Dropdown_Class extends Moraso_Module_Abstract {

    protected $_renderOnlyAllowed = true;

    protected function _main() {

        $shortcodes = array();

        foreach (Aitsu_Config::get('edit.ckeditor.shortcode') as $key => $shortcode) {
            $shortcodes[] = array(
                'key' => $key,
                'label' => $shortcode->label,
                'shortcode' => $shortcode->shortcode
            );
        }

        return json_encode($shortcodes);
    }

    protected function _cachingPeriod() {

        return 'eternal';
    }

}