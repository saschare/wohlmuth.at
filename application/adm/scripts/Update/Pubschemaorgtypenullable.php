<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2012, webtischlerei
 */
class Adm_Script_Pubschemaorgtypenullable extends Aitsu_Adm_Script_Abstract {

    public static function getName() {

        return Aitsu_Translate :: translate('Remove "nullable schemaorgtype" bug in "_pub_art_meta"');
    }

    public function doRemoveBug() {

        Aitsu_Db::query('' .
                'update _pub_art_meta ' .
                'set schemaorgtype = replace(schemaorgtype, 0, NULL) ' .
                'where schemaorgtype = 0');

        return Aitsu_Adm_Script_Response::factory(Aitsu_Translate :: translate('Bug fixed!'));
    }

}