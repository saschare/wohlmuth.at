<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Init_Config_Db implements Aitsu_Event_Listener_Interface {

    public static function notify(Aitsu_Event_Abstract $event) {
        
        if (!empty($_SERVER['PHP_FCGI_CHILDREN']) || !empty($_SERVER['FCGI_ROLE'])) {
            $env = (getenv("REDIRECT_AITSU_ENV") == '' ? 'live' : getenv("REDIRECT_AITSU_ENV"));
        } else {
            $env = (getenv("AITSU_ENV") == '' ? 'live' : getenv("AITSU_ENV"));
        }
        
        $client = Aitsu_Mapping::getIni();

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
            return '';
        }

        $database_array = array();

        foreach ($database_config as $row) {
            $rowConfig[$row['env']] = Aitsu_Util::parseSimpleIni($row['identifier'] . ' = ' . $row['value']);

            $database_array = array_merge_recursive((array) $database_array, (array) $rowConfig);
        }

        $config = new Zend_Config_Json(json_encode($database_array), $env, array(
            'allowModifications' => true
        ));
        
        Aitsu_Registry :: get()->config->merge($config);
    }

}