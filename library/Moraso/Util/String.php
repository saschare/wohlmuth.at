<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @category Moraso
 * @package Util
 * @subpackage String
 * 
 * @since 1.2.4-1
 */
class Moraso_Util_String {

    /**
     * The perfect PHP clean url generator
     * 
     * Here is an example:
     * <pre><code>
     * <?php
     * $slug = Moraso_Util_String::slugify("Hällö Foo");
     * 
     * echo $slug; // haelloe-foo
     * ?>
     * </code></pre>
     * 
     * @example http://www.moraso.de/doc/functions/util/string/slugify.html How to use this function
     * @since 1.2.4-1
     * @param string $string String to slugify
     * @return string $slug slugified string
     * 
     * @ex
     */
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