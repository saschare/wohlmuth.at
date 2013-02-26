<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Util extends Aitsu_Util {

    public static function getIdlang() {

        $idlang = Aitsu_Registry :: get()->env->idlang;

        if (empty($idlang)) {
            $idlang = Aitsu_Registry :: get()->session->currentLanguage;
        }

        return $idlang;
    }

}