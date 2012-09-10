<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * 
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @version 1.1
 * @since 2.2.x
 */
class Aitsu_Placeholder {

    public static function get($identifier) {

        $idlang = Aitsu_Registry::isFront() ? Aitsu_Registry::get()->env->idlang : Aitsu_Registry::get()->session->currentLanguage;

        if (!is_numeric($identifier)) {
            $value = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   value.value ' .
                            'from ' .
                            '   _placeholder as placeholder ' .
                            'inner join ' .
                            '   _placeholder_values as value on (' .
                            '       value.placeholderid = placeholder.id ' .
                            '   and ' .
                            '       value.idlang =:idlang ' .
                            '   ) ' .
                            'where ' .
                            '   placeholder.identifier =:identifier', array(
                        ':identifier' => $identifier,
                        ':idlang' => $idlang
                    ));
        } else {
            $value = Aitsu_Db::fetchOneC('eternal', '' .
                            'select ' .
                            '   identifier ' .
                            'from ' .
                            '   _placeholder ' .
                            'where ' .
                            '   id =:identifier', array(
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

        $id = Aitsu_Db::fetchOneC('eternal', '' .
                        'select ' .
                        '   id ' .
                        'from ' .
                        '   _placeholder_values ' .
                        'where ' .
                        '   idlang =:idlang ' .
                        'and ' .
                        '   placeholderid =:placeholderid', array(
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