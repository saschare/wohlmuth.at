<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @since 1.2.3-8
 */
class Moraso_Util {

    public static function slugify($string) {

        $in_charset = mb_detect_encoding($string);
        $out_charset = 'ASCII//TRANSLIT';

        $slug_trim = trim($string);

        $slug_tolower = strtolower($slug_trim);

        $slug_iconv = iconv($in_charset, $out_charset, $slug_tolower);

        $slug_whitespace = preg_replace("%[^-/+|\w ]%", '', $slug_iconv);

        $slug = preg_replace("/[\/_|+ -]+/", '-', $slug_whitespace);

        return $slug;
    }

}