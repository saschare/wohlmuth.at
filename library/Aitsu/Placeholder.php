<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Aitsu_Placeholder {

    public static function get($placeholder) {

        if (Aitsu_Registry::isFront()) {
            $idlang = Aitsu_Registry::get()->env->idlang;
        } else {
            $idlang = Aitsu_Registry::get()->session->currentLanguage;
        }

        $value = Aitsu_Db::fetchOne("
            SELECT
                `value`
            FROM
                `_placeholder`
            WHERE
                `placeholder` =:placeholder
            AND
                `idlang` =:idlang
       ", array(
           ':placeholder' => $placeholder,
           ':idlang' => $idlang
       ));

        if (empty($value)) {
            $value = 'Placeholder: ' . $placeholder;
        }

        return $value;
    }

    public static function set($placeholder, $value, $edit = null) {

        if (Aitsu_Registry::isFront()) {
            $idlang = Aitsu_Registry::get()->env->idlang;
        } else {
            $idlang = Aitsu_Registry::get()->session->currentLanguage;
        }

        $data = array(
            'placeholder' => $placeholder,
            'value' => $value,
            'idlang' => $idlang
        );

        if (!empty($edit)) {
            $data['id'] = $edit;
        }

        Aitsu_Db::put(`_placeholder`, 'id', $data);
    }

}