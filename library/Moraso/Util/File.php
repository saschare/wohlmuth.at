<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Util_File {

    public static function rename($old, $new) {
        if (is_file($old)) {
            rename($old, $new);
        }
    }

}