<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Mobile_Detect implements Aitsu_Event_Listener_Interface {

    public static function notify(Aitsu_Event_Abstract $event) {

        $detect = new Mobile_Detect();

        Aitsu_Registry::get()->env->mobile->detect->isMobile = (bool) $detect->isMobile();
        Aitsu_Registry::get()->env->mobile->detect->isTablet = (bool) $detect->isTablet();

        $isMobile = (string) Aitsu_Registry::get()->env->mobile->detect->isMobile;
        $isTablet = (string) Aitsu_Registry::get()->env->mobile->detect->isTablet;

        Aitsu_Registry::get()->env->mobile->detect->isMobile = empty($isMobile) ? 'isNot' : 'is';
        Aitsu_Registry::get()->env->mobile->detect->isTablet = empty($isTablet) ? 'isNot' : 'is';
    }

}
