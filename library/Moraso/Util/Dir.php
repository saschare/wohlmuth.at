<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @category Moraso
 * @package Util
 * @subpackage Dir
 * 
 * @since 1.2.3-1
 */
class Moraso_Util_Dir {

    public static function copy($source, $target) {
        if (is_dir($source)) {
            @mkdir($target);

            $d = dir($source);

            while (FALSE !== ( $entry = $d->read() )) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $Entry = $source . '/' . $entry;
                if (is_dir($Entry)) {
                    $this->full_copy($Entry, $target . '/' . $entry);
                    continue;
                }
                copy($Entry, $target . '/' . $entry);
            }

            $d->close();
        } else {
            copy($source, $target);
        }
    }

}