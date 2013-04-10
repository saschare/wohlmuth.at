<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Adm_Script_SyncAllArticles extends Aitsu_Adm_Script_Abstract {

    public static function getName() {

        return Aitsu_Translate::translate('Sync all Articles');
    }

    protected function doSync() {

        $target = Aitsu_Registry::get()->session->currentLanguage;
        $origin = 1;

        if ($origin != $target) {
            $articles = Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   idart ' .
                            'from ' .
                            '   _art_lang ' .
                            'where ' .
                            '   idlang = :idlang ', array(
                        ':idlang' => $origin
            ));

            foreach ($articles as $article) {
                Aitsu_Persistence_Article::factory($article['idart'], $target)->sync($origin);
            }

            return Aitsu_Adm_Script_Response::factory('Alle Artikel wurden synchronisiert!');
        } else {
            return Aitsu_Adm_Script_Response::factory('Ursprungs und Zielsprache sind identisch!');
        }
    }

}