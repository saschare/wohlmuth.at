<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Aitsu_Placeholder {

    public static function get($identifier) {

        if (Aitsu_Registry::isFront()) {
            $idlang = Aitsu_Registry::get()->env->idlang;
        } else {
            $idlang = Aitsu_Registry::get()->session->currentLanguage;
        }

        if (!is_numeric($identifier)) {
            $value = Aitsu_Db::fetchOne("
            SELECT
                `value`.`value`
            FROM
                `_placeholder` AS `placeholder`
            INNER JOIN
                `_placeholder_values` AS `value` ON (
                    `value`.`placeholderid` = `placeholder`.`id`
                 AND
                    `value`.`idlang` =:idlang
                )
            WHERE
                `placeholder`.`identifier` = :identifier
            ", array(
                        ':identifier' => $identifier,
                        ':idlang' => $idlang
                    ));
        } else {
            $value = Aitsu_Db::fetchOne("
            SELECT
                `identifier`
            FROM
                `_placeholder`
            WHERE
                `id` =:identifier
            ", array(
                        ':identifier' => $identifier
                    ));
        }

        return $value;
    }

    public static function set($identifier, $value, $id = null) {

        if (!empty($id)) {
            $data['id'] = $id;
        }

        $data['identifier'] = $identifier;

        $placeholderid = Aitsu_Db::put('_placeholder', 'id', $data);

        unset($data);

        $id = Aitsu_Db::fetchOne("SELECT `id` FROM `_placeholder_values` WHERE `idlang` =:idlang AND `placeholderid` =:placeholderid", array(
                    ':placeholderid' => $placeholderid,
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
                ));

        if (!empty($id)) {
            $data['id'] = $id;
        }

        $data['placeholderid'] = $placeholderid;
        $data['idlang'] = Aitsu_Registry::get()->session->currentLanguage;
        $data['value'] = $value;

        Aitsu_Db::put('_placeholder_values', 'id', $data);
    }

}