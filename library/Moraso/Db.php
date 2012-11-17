<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Db {

    /**
     * Based on Aitsu_Db::filter()
     * Extended by variable $groups
     * 
     * @since 1.2.6-1
     * @see Aitsu_Db::filter();
     */
    public static function filter($baseQuery, $limit = null, $offset = null, $filters = null, $orders = null, $groups = null) {

        $limit = is_null($limit) || !is_numeric($limit) ? 100 : $limit;
        $offset = is_null($offset) || !is_numeric($offset) ? 0 : $offset;
        $filters = is_array($filters) ? $filters : array();
        $orders = is_array($orders) ? $orders : array();
        $groups = is_array($groups) ? $groups : array();

        $filterClause = array();
        $filterValues = array();
        for ($i = 0; $i < count($filters); $i++) {
            $filterClause[] = $filters[$i]->clause . ' :value' . $i;
            $filterValues[':value' . $i] = $filters[$i]->value;
        }
        $where = count($filterClause) == 0 ? '' : 'where ' . implode(' and ', $filterClause);

        $orderBy = count($orders) == 0 ? '' : 'order by ' . implode(', ', $orders);
        $groupBy = count($groups) == 0 ? '' : 'group by ' . implode(', ', $groups);

        $results = Aitsu_Db::fetchAll('' .
                        $baseQuery .
                        ' ' . $where .
                        ' ' . $groupBy .
                        ' ' . $orderBy .
                        'limit ' . $offset . ', ' . $limit, $filterValues);

        $return = array();

        if ($results) {
            foreach ($results as $result) {
                $return[] = (object) $result;
            }
        }

        return $return;
    }

}