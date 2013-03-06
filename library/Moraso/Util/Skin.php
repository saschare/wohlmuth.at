<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Util_Skin {

    public static function buildHeredity(& $heredity = null, $skin = null) {

        if (empty($heredity)) {
            $heredity = array();
        }

        if (empty($skin)) {
            $skin = Aitsu_Registry :: get()->config->skin;
        }

        $heredity[] = $skin;

        $xml_file = APPLICATION_PATH . '/skins/' . $skin . '/skin.xml';

        if (is_readable($xml_file)) {
            $xml = simplexml_load_file($xml_file);

            $parentSkin = (string) $xml->parent->skin[0];

            if (!empty($parentSkin)) {
                self::buildHeredity($heredity, $parentSkin);
            }
        }

        return $heredity;
    }

}