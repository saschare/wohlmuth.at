<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Status {

    public static function version() {

        $version = '$version/1.6.0/revision/1$';

        return str_replace(array(
            'version/',
            '/revision/',
            '$'
                ), array(
            '',
            '-',
            ''
                ), $version);
    }

}