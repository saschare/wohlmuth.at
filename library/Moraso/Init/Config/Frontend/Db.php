<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Init_Config_Frontend_Db implements Aitsu_Event_Listener_Interface {

    public static function notify(Aitsu_Event_Abstract $event) {
        $env = Moraso_Util::getEnv();
        $client = Aitsu_Mapping::getIni();

        Moraso_Config_Db::setConfigFromDatabase($env, $client);
    }

}