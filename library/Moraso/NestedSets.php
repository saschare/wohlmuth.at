<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_NestedSets {

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $parent
     * @param type $next
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::move(17, 13);
     * @example Moraso_NestedSets::move(17, 0, 14);
     * @example Moraso_NestedSets::move(17, 18, 31, '_example_table');
     * 
     * @example Moraso_NestedSets::move(array('lft' => 10, 'rgt' => 13), 13);
     */
    public static function move($node, $parent, $next = 0, $table = '_cat') {

        $primary = self::_getPrimaryKey($table);

        if (!is_array($node)) {
            $node = self::getNodeLftRgt($node, $table, $primary);
        }

        if (empty($parent) && empty($next)) {
            $target = Aitsu_Db::fetchOne('select max(rgt) +1 from ' . $table . '');
        } elseif ((!empty($next))) {
            $target = Aitsu_Db::fetchOne('select lft from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $next));
        } else {
            $target = Aitsu_Db::fetchOne('select rgt from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $parent));
        }

        Aitsu_Db::startTransaction();

        try {
            $query = '' .
                    'update ' . $table . ' ' .
                    '   set lft = lft + if (:target > :rgt, ' .
                    '       if (:rgt < lft and lft < :target, :lft - :rgt - 1, ' .
                    '           if (:lft <= lft and lft < :rgt, :target - :rgt - 1, 0) ' .
                    '       ), ' .
                    '       if (:target <= lft and lft < :lft, :rgt - :lft + 1, ' .
                    '           if (:lft <= lft and lft < :rgt, :target - :lft, 0) ' .
                    '       ) ' .
                    '   ), ' .
                    '   rgt = rgt + if (:target > :rgt, ' .
                    '       if (:rgt < rgt and rgt < :target, :lft - :rgt - 1, ' .
                    '           if (:lft < rgt and rgt <= :rgt, :target - :rgt - 1, 0) ' .
                    '       ), ' .
                    '       if (:target <= rgt and rgt < :lft, :rgt - :lft + 1, ' .
                    '           if (:lft < rgt and rgt <= :rgt, :target - :lft, 0) ' .
                    '       ) ' .
                    '   ) ' .
                    'where ' .
                    '   :rgt < :target ' .
                    'or ' .
                    '   :target < :lft';

            $search = array(':target', ':lft', ':rgt');
            $replace = array($target, $node['lft'], $node['rgt']);

            Aitsu_Db::query(str_replace($search, $replace, $query));
        } catch (Exception $e) {
            Aitsu_Db::rollback();
            trigger_error($e->getMessage());
            return false;
        }

        Aitsu_Db::commit();
        return true;
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::delete(17);
     * @example Moraso_NestedSets::delete(17, '_example_table');
     */
    public static function delete($node, $table = '_cat') {

        if (!is_array($node)) {
            $node = self::getNodeLftRgt($node, $table);
        }

        Aitsu_Db::startTransaction();

        try {
            Aitsu_Db::query('' .
                    'delete from ' .
                    ' ' . $table . ' ' .
                    'where ' .
                    ' lft between ' . $node['lft'] . ' and ' . $node['rgt']);

            Aitsu_Db::query('' .
                    'update ' .
                    ' ' . $table . ' ' .
                    'set ' .
                    ' lft = lft - round((' . $node['rgt'] . ' - ' . $node['lft'] . ' + 1)) ' .
                    'where ' .
                    ' lft > ' . $node['rgt']);

            Aitsu_Db::query('' .
                    'update ' .
                    ' ' . $table . ' ' .
                    'set ' .
                    ' rgt = rgt - round((' . $node['rgt'] . ' - ' . $node['lft'] . ' + 1)) ' .
                    'where ' .
                    ' rgt > ' . $node['rgt']);
        } catch (Exception $e) {
            Aitsu_Db::rollback();
            trigger_error($e->getMessage());
            return false;
        }

        Aitsu_Db::commit();
        return true;
    }

    /**
     * Description ...
     * 
     * @param array $data
     * @param type $parent
     * @param type $table
     * @return boolean 
     * 
     * @example Moraso_NestedSets::insert(array('name' => 'Example 1'));
     * @example Moraso_NestedSets::insert(array('name' => 'Example 2'), 17);
     * @example Moraso_NestedSets::insert(array('name' => 'Example 3'), 13, '_example_table');
     */
    public static function insert(array $data, $parent = null, $table = '_cat') {

        $primary = self::_getPrimaryKey($table);

        unset($data[$primary]);

        if (!empty($parent)) {
            $parent = self::getNodeLftRgt($parent, $table);
        } else {
            $parent = Aitsu_Db::fetchRow('select max(rgt) + 1 as rgt from ' . $table);
        }

        $data['lft'] = $parent['rgt'];
        $data['rgt'] = $parent['rgt'] + 1;

        Aitsu_Db::startTransaction();

        try {
            Aitsu_Db::query('' .
                    'update ' .
                    '   ' . $table . ' ' .
                    'set ' .
                    '   rgt = rgt + 2 ' .
                    'where ' .
                    '   rgt >= :rgt', array(
                ':rgt' => $parent['rgt']
            ));

            Aitsu_Db::query('' .
                    'update ' .
                    '   ' . $table . ' ' .
                    'set ' .
                    '   lft = lft + 2 ' .
                    'where ' .
                    '   lft >= :rgt', array(
                ':rgt' => $parent['rgt']
            ));

            Aitsu_Db::put($table, $primary, $data);
        } catch (Exception $e) {
            Aitsu_Db::rollback();
            trigger_error($e->getMessage());
            return false;
        }

        Aitsu_Db::commit();
        return true;
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $level
     * @param type $table
     * @return type 
     * 
     * @example Moraso_NestedSets::treesource();
     * @example Moraso_NestedSets::treesource(17, 1);
     * @example Moraso_NestedSets::treesource(21, 2, '_example_table');
     */
    public static function treesource($node = null, $level = 0, $table = '_cat') {

        $primary = self::_getPrimaryKey($table);

        $categories = array();

        if (!empty($node)) {
            $categories = Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   o.' . $primary . ', ' .
                            '   o.name, ' .
                            '   count(p.' . $primary . ')-1 as level ' .
                            'from ' .
                            '   ' . $table . ' as n, ' .
                            '   ' . $table . ' as p, ' .
                            '   ' . $table . ' as o ' .
                            'where ' .
                            '   o.lft between p.lft and p.rgt ' .
                            'and ' .
                            '   o.lft between n.lft and n.rgt ' .
                            'and ' .
                            '   n.' . $primary . ' =:id ' .
                            'group by ' .
                            '   o.lft ' .
                            'having ' .
                            '   level =:level ' .
                            'order by ' .
                            '   o.lft asc', array(
                        ':id' => $node,
                        ':level' => $level + 1
            ));
        } else {
            $categories = Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   n.' . $primary . ', ' .
                            '   n.name, ' .
                            '   count(*)-1 as level ' .
                            'from ' .
                            '   ' . $table . ' as n, ' .
                            '   ' . $table . ' as p ' .
                            'where ' .
                            '   n.lft between p.lft and p.rgt ' .
                            'group by ' .
                            '   n.' . $primary . ' ' .
                            'having ' .
                            '   level = 0 ' .
                            'order by ' .
                            '   n.lft asc');
        }

        $data = array();
        foreach ($categories as $category) {
            $leaf = false;
            $isOnline = true;
            $iconClas = 'treecat-' . ($isOnline ? 'online' : 'offline');

            $data[] = array(
                'id' => $category[$primary],
                'idcat' => $category[$primary],
                'idcategory' => $category[$primary],
                'text' => $category['name'],
                'leaf' => $leaf,
                'iconCls' => $iconClas,
                'type' => 'category',
                'level' => $category['level']
            );
        }

        return $data;
    }

    /**
     * Description ...
     * 
     * @param type $table
     * @return type 
     * 
     * @example self::_getPrimaryKey('_example_table');
     */
    private static function _getPrimaryKey($table) {

        $indexes = Aitsu_Db::fetchRowC('eternal', '' .
                        'show ' .
                        ' indexes ' .
                        'from ' .
                        ' ' . $table . ' ' .
                        'where ' .
                        ' Key_name = :key_name', array(
                    ':key_name' => 'PRIMARY'
                        )
        );

        return $indexes['Column_name'];
    }

    /**
     * Description ...
     * 
     * @param type $node
     * @param type $table
     * @param type $primary
     * @return type 
     * 
     * @example self::getNodeLftRgt(17, '_example_table');
     * @example self::getNodeLftRgt(21, '_example_table', 'idexample');
     */
    public static function getNodeLftRgt($node, $table, $primary = null) {

        if (empty($primary)) {
            $primary = self::_getPrimaryKey($table);
        }

        return Aitsu_Db::fetchRow('select lft, rgt from ' . $table . ' where ' . $primary . ' =:id', array(':id' => $node));
    }

}