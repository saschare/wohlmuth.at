<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Adm_Script_SyncAllCategories extends Aitsu_Adm_Script_Abstract {

    public static function getName() {

        return Aitsu_Translate::translate('Sync all Categories');
    }

    protected function doSync() {

        $target = Aitsu_Registry::get()->session->currentLanguage;
        $origin = 1;

        if ($origin != $target) {
            $categories = Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   idcat ' .
                            'from ' .
                            '   _cat_lang ' .
                            'where ' .
                            '   idlang = :idlang ', array(
                        ':idlang' => $origin
            ));

            foreach ($categories as $category) {
                Aitsu_Persistence_Category::factory($category['idcat'])->synchronize($origin);
            }

            return Aitsu_Adm_Script_Response::factory('Alle Kategorien wurden synchronisiert!');
        } else {
            return Aitsu_Adm_Script_Response::factory('Ursprungs und Zielsprache sind identisch!');
        }
    }

}