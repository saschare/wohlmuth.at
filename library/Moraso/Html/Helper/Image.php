<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Html_Helper_Image {

    public static function getHtml($idart, $filename, $width, $height, $boxed, $attributes) {

        $attributes = (object) $attributes;
        
        $return = '<img src="/image/' . $width . '/' . $height . '/' . $boxed . '/' . $idart . '/' . $filename . '"';

        if (!empty($attributes)) {
            foreach ($attributes as $attr => $value) {
                if (is_object($value)) {
                    $return .= ' ' . $attr . '="';
                    foreach ($value as $style => $styleValue) {
                        if (!empty($styleValue)) {
                            if ($style == 'self') {
                                $return .= $styleValue;
                            } else {
                                $return .= '' . $style . ': ' . $styleValue . ';';
                            }
                        }
                    }
                    $return .= '"';
                } else {
                    if (!empty($value)) {
                        $return .= ' ' . $attr . '="' . $value . '"';
                    }
                }
            }
        }
        $return .= ' />';

        return $return;
    }

}