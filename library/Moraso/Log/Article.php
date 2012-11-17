<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @category Moraso
 * @package Log
 * @subpackage Article
 * 
 * @since 1.3.0-1
 */
class Moraso_Log_Article implements Aitsu_Event_Listener_Interface {

    public static function notify(Aitsu_Event_Abstract $event) {

        $idartlang = $event->idartlang;
        $action = $event->action;
        $idart = Aitsu_Util::getIdArt($idartlang);
        $user = Aitsu_Adm_User::getInstance();
        
        $article = Aitsu_Persistence_Article::factory($idart);

        switch ($action) {
            case 'save':
                trigger_error(sprintf(Aitsu_Translate::_('%s has saved article "%s" (idart %s).'), $user->firstname . ' ' . $user->lastname, $article->pagetitle, $idart));
                break;
        }
    }

}