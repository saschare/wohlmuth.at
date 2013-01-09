<?php

/**
 * @author Christian Kehres, <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @version 2.0
 * @since aitsu 2.1
 */
class Module_Shortcodes_Dropdown_Class extends Aitsu_Module_Abstract {

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