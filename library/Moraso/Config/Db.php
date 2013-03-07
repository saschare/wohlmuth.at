<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Config_Db {

    public static function setConfigFromDatabase($client) {

        $env = Moraso_Util::getEnv();
        
        $database_config = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   env, ' .
                        '   identifier, ' .
                        '   value ' .
                        'from ' .
                        '   _moraso_config ' .
                        'where ' .
                        '   client =:client', array(
                    ':client' => $client
        ));

        if (empty($database_config)) {
            return;
        }

        $database_array = array(
            'default' => array(),
            'live' => array(
                '_extends' => 'default'
            ),
            'prod' => array(
                '_extends' => 'live'
            ),
            'staging' => array(
                '_extends' => 'prod'
            ),
            'preprod' => array(
                '_extends' => 'staging'
            ),
            'dev' => array(
                '_extends' => 'preprod'
            )
        );

        foreach ($database_config as $row) {
            $rowConfig[$row['env']] = Aitsu_Util::parseSimpleIni($row['identifier'] . ' = ' . $row['value']);

            $database_array = array_merge_recursive((array) $database_array, (array) $rowConfig);

            unset($rowConfig);
        }

        $config = new Zend_Config_Json(json_encode($database_array), $env, array(
            'allowModifications' => true
        ));

        Aitsu_Registry :: get()->config->merge($config);
    }

}