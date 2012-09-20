<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3conceps AG
 */
class Webtischlerei_Status {

    public static function version() {

        $version = '$version/1.0.0/revision/7$';

        $version = str_replace(array(
            'version/',
            '/revision/',
            '$'
                ), array(
            '',
            '-',
            ''
                ), $version);

        return $version;
    }

}