<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Eav {

    public static function createEntity() {

        return Aitsu_Db::query('insert into _eav_entity (entity_id) values (NULL)')->getLastInsertId();
    }

    public static function checkIfAttributeAlreadyExist($attribute_set_id, $attribute_alias) {

        $attribute_id = self::getAttributeIdByAlias($attribute_set_id, $attribute_alias);

        if (!$attribute_id) {
            $attribute_id = self::createAttribute($attribute_set_id, $attribute_alias);
        }

        return $attribute_id;
    }

    public static function checkIfAttributeSetAlreadyExist($attribute_set_name) {

        $attribute_set_id = self::getAttributeSetId($attribute_set_name);

        if (!$attribute_set_id) {
            $attribute_set_id = self::createAttributeSet($attribute_set_name);
        }

        return $attribute_set_id;
    }

    public static function createAttribute($set_id, $alias) {

        return Aitsu_Db::query('' .
                        'insert into ' .
                        '   _eav_attribute ' .
                        '   (attribute_alias, attribute_set_id) ' .
                        'values ' .
                        '   ("' . $alias . '", "' . $set_id . '")'
                )->getLastInsertId();
    }
    
    public static function createAttributeSet($set_name) {

        return Aitsu_Db::query('' .
                        'insert into ' .
                        '   _eav_attribute_set ' .
                        '   (attribute_set_name) ' .
                        'values ' .
                        '   ("' . $set_name . '")'
                )->getLastInsertId();
    }

    public static function deleteEntity($id) {

        Aitsu_Db::query('' .
                'delete from ' .
                '   _eav_entity ' .
                'where ' .
                '   entity_id =:id', array(
            ':id' => $id
        ));
    }

    public static function setValues($attribute_set, $data) {

        $id = $data['id'];

        unset($data['id']);
        foreach ($data as $attribute_alias => $value) {

            $attribute_set_id = self::checkIfAttributeSetAlreadyExist($attribute_set);
            
            $attribute_id = self::checkIfAttributeAlreadyExist($attribute_set_id, $attribute_alias);

            $originalValueLength = strlen($value);

            if ($originalValueLength == strlen(intval($value))) {
                $type = 'integer';
            } elseif ($originalValueLength == strlen(floatval($value))) {
                $type = 'float';
            } else {
                $type = 'string';
            }

            self::setValue($id, $attribute_id, $value, $type);
        }
    }

    public static function getAttributeIdByAlias($set_id, $alias) {

        return Aitsu_Db::fetchOne('' .
                        'select ' .
                        '   attribute_id ' .
                        'from ' .
                        '   _eav_attribute ' .
                        'where ' .
                        '   attribute_alias =:alias ' .
                        'and ' .
                        '   attribute_set_id =:set_id', array(
                    ':alias' => $alias,
                    ':set_id' => $set_id
                ));
    }

    public static function getAttributeSetId($set_name) {

        return Aitsu_Db::fetchOne('' .
                        'select ' .
                        '   attribute_set_id ' .
                        'from ' .
                        '   _eav_attribute_set ' .
                        'where ' .
                        '   attribute_set_name =:set_name', array(
                    ':set_name' => $set_name
                ));
    }

    public static function setValue($entity_id, $attribute_id, $value, $type = 'string') {

        $entity_attribute_id = Aitsu_Db::fetchOne('' .
                        'select ' .
                        '   entity_attribute_id ' .
                        'from ' .
                        '   _eav_entity_attribute ' .
                        'where ' .
                        '   entity_id =:entity_id ' .
                        'and ' .
                        '   attribute_id =:attribute_id', array(
                    ':entity_id' => $entity_id,
                    ':attribute_id' => $attribute_id
                ));


        if (!empty($entity_attribute_id)) {
            $value_id = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   value_id ' .
                            'from ' .
                            '   _eav_value ' .
                            'where ' .
                            '   entity_attribute_id =:entity_attribute_id ', array(
                        ':entity_attribute_id' => $entity_attribute_id
                            )
            );
        } else {
            $entity_attribute_id = Aitsu_Db::query('' .
                            'insert into ' .
                            '   _eav_entity_attribute ' .
                            '   (entity_id, attribute_id) ' .
                            'values ' .
                            '   (' . $entity_id . ', ' . $attribute_id . ')'
                    )->getLastInsertId();

            $value_id = Aitsu_Db::query('' .
                            'insert into ' .
                            '   _eav_value ' .
                            '   (entity_attribute_id) ' .
                            'values ' .
                            '   (' . $entity_attribute_id . ')'
                    )->getLastInsertId();
        }

        $data = array(
            'value_id' => $value_id,
            'value' => $value
        );

        Aitsu_Db::query('delete from _eav_value_string where value_id =:valueid', array(':valueid' => $value_id));
        Aitsu_Db::query('delete from _eav_value_integer where value_id =:valueid', array(':valueid' => $value_id));
        Aitsu_Db::query('delete from _eav_value_float where value_id =:valueid', array(':valueid' => $value_id));

        Aitsu_Db::put('_eav_value_' . $type, NULL, $data);
    }

    public static function getEntityData($attribute_set, $entity_id) {

        $attribute_set_id = self::getAttributeSetId($attribute_set);
        
        $rows = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   a.attribute_alias, ' .
                        '   coalesce( ' .
                        '       vs.value, ' .
                        '       vi.value, ' .
                        '       vf.value ' .
                        '   ) as value ' .
                        'from ' .
                        '   _eav_entity as e ' .
                        'left join ' .
                        '   _eav_entity_attribute as ea on ea.entity_id = e.entity_id ' .
                        'left join ' .
                        '   _eav_attribute as a on a.attribute_id = ea.attribute_id ' .
                        'left join ' .
                        '   _eav_value as v on v.entity_attribute_id = ea.entity_attribute_id ' .
                        'left join ' .
                        '   _eav_value_string as vs on vs.value_id = v.value_id ' .
                        'left join ' .
                        '   _eav_value_integer as vi on vi.value_id = v.value_id ' .
                        'left join ' .
                        '   _eav_value_float as vf on vf.value_id = v.value_id ' .
                        'where ' .
                        '   ea.entity_id =:entity_id ' .
                        'and ' .
                        '   a.attribute_set_id =:attribute_set_id', array(
                    ':attribute_set_id' => $attribute_set_id,
                    ':entity_id' => $entity_id
                        )
        );

        $data = array();
        foreach ($rows as $row) {
            $data[$row['attribute_alias']] = $row['value'];
        }

        return $data;
    }

    public static function getAllData($set_name) {
        
        $attribute_set_id = self::getAttributeSetId($set_name);

        $rows = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   e.entity_id as id, ' .
                        '   a.attribute_alias, ' .
                        '   coalesce( ' .
                        '       vs.value, ' .
                        '       vi.value, ' .
                        '       vf.value ' .
                        '   ) as value ' .
                        'from ' .
                        '   _eav_entity as e ' .
                        'left join ' .
                        '   _eav_entity_attribute as ea on ea.entity_id = e.entity_id ' .
                        'left join ' .
                        '   _eav_attribute as a on a.attribute_id = ea.attribute_id ' .
                        'left join ' .
                        '   _eav_value as v on v.entity_attribute_id = ea.entity_attribute_id ' .
                        'left join ' .
                        '   _eav_value_string as vs on vs.value_id = v.value_id ' .
                        'left join ' .
                        '   _eav_value_integer as vi on vi.value_id = v.value_id ' .
                        'left join ' .
                        '   _eav_value_float as vf on vf.value_id = v.value_id ' .
                        'where ' .
                        '   a.attribute_set_id =:attribute_set_id', array(
                    ':attribute_set_id' => $attribute_set_id
                        )
        );

        $data = array();
        foreach ($rows as $row) {
            $data[$row['id']][$row['attribute_alias']] = $row['value'];
        }

        foreach ($rows as $row) {
            $data[$row['id']]['id'] = $row['id'];
        }

        sort($data);

        return $data;
    }

}