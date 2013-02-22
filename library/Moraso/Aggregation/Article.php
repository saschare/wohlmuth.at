<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Aggregation_Article extends Aitsu_Aggregation_Article {

    public function fetch($offset = 0, $limit = 100) {

        $this->_fetchResults($offset, $limit);

        return $this;
    }

    protected function _fetchResults($offset, $limit) {

        if (count($this->_results) > 0 && $this->_offset == $offset && $this->_limit = $limit) {
            return;
        }

        if ($this->_fetches != null || $this->_properties != null || $this->_media != null) {
            $join = array();
            $select = array();
            if ($this->_fetches != null) {
                foreach ($this->_fetches as $alias => $type) {
                    $join[] = "left join _article_content as tbl{$alias} on artlang.idartlang = tbl{$alias}.idartlang and tbl{$alias}.index = '{$type->name}' ";
                    $select[] = "tbl{$alias}.value as {$alias} ";
                }
            }
            if ($this->_properties != null) {
                foreach ($this->_properties as $alias => $property) {
                    $join[] = "left join _aitsu_property as spro{$alias} on spro{$alias}.identifier = '{$property->alias}' left join _aitsu_article_property as tbl{$alias} on tbl{$alias}.idartlang = artlang.idartlang and spro{$alias}.propertyid = tbl{$alias}.propertyid ";
                    $select[] = "tbl{$alias}.{$property->type} as {$alias} ";
                }
            }
            if ($this->_media != null) {
                foreach ($this->_media as $alias => $filter) {
                    $join[] = "left join _media as tbl{$alias} on artlang.idart = tbl{$alias}.idart and tbl{$alias}.filename like '{$filter->filter}' and tbl{$alias}.deleted is null left join _media_description as tbl{$alias}2 on tbl{$alias}.mediaid = tbl{$alias}2.mediaid ";
                    $select[] = "tbl{$alias}.mediaid as f_mediaid_{$alias}, tbl{$alias}.filename as f_filename_{$alias}, tbl{$alias}.uploaded as f_uploaded_{$alias},  tbl{$alias}2.name as f_name_{$alias}, tbl{$alias}2.subline as f_subline_{$alias}, tbl{$alias}2.description as f_description_{$alias} ";
                }
            }
            $selects = ', ' . implode(', ', $select);
            $joins = implode('', $join);
        } else {
            $selects = '';
            $joins = '';
        }

        if ($this->_whereInCategories != null) {
            $whereInCategories = 'and catart.idcat in (' . implode(', ', $this->_whereInCategories) . ') ';
        } else {
            $whereInCategories = '';
        }

        switch ($this->_startArticle) {
            case 2 :
                $useOfStartArticle = ' and (catlang.startidartlang != artlang.idartlang or catlang.startidartlang is null) ';
                break;
            case 3 :
                $useOfStartArticle = ' and catlang.startidartlang = artlang.idartlang ';
                break;
            default :
                $useOfStartArticle = ' ';
        }

        if ($this->_filters == null) {
            $where = '';
        } else {
            $where = ' and ' . implode(' and ', $this->_filters);
        }

        $orderAlias['modified'] = 'artlang.lastmodified';
        $orderAlias['created'] = 'artlang.created';
        $orderAlias['artsort'] = 'artlang.artsort';
        if ($this->_orderBy != null) {
            $orderBy = 'order by ';
            $order = array();
            foreach ($this->_orderBy as $alias => $ascending) {
                $order[] = $alias . ' ' . ($ascending ? 'asc' : 'desc');
            }
            $orderBy .= implode(', ', $order);
        } else {
            $orderBy = '';
        }

        if ($this->_media != null) {
            $orderBy = $orderBy == '' ? $orderBy : $orderBy . ', ';
            $orderByAddOns = array();
            foreach ($this->_media as $alias => $value) {
                $orderByAddOns[] = "tbl{$alias}.mediaid desc";
            }
            $orderBy .= implode(', ', $orderByAddOns);
        }

        $results = Aitsu_Db :: fetchAll("" .
                        "select straight_join " .
                        "	artlang.idart idart, " .
                        "	artlang.idartlang idartlang, " .
                        "	artlang.title articletitle, " .
                        "	artlang.pagetitle pagetitle, " .
                        "	artlang.teasertitle teasertitle, " .
                        "	artlang.summary summary, " .
                        "	artlang.created created, " .
                        "	unix_timestamp(artlang.created) ts_created, " .
                        "	artlang.lastmodified modified," .
                        "	artlang.artsort artsort, " .
                        "	artlang.mainimage, " .
                        "	artlang.pubfrom pubfrom, " .
                        "	artlang.pubuntil pubuntil, " .
                        "	artlang.mainimage mainimage, " .
                        "	meta.date as metadate, " .
                        "	coord.lat lat, " .
                        "	coord.lng lng, " .
                        "	if(artlang.redirect = 1 and substr(artlang.redirect_url, 1, 4) = 'http', artlang.redirect_url, null) redirect " .
                        "	{$selects} " .
                        "from _art_lang artlang " .
                        "{$joins} " .
                        "left join _art_meta as meta on meta.idartlang = artlang.idartlang " .
                        "left join _cat_art catart on artlang.idart = catart.idart " .
                        "left join _cat_lang catlang on catart.idcat = catlang.idcat AND catlang.idlang = :idlang " .
                        "left join _art_geolocation artcoord on artlang.idartlang = artcoord.idartlang " .
                        "left join _google_geolocation coord on artcoord.idlocation = coord.id " .
                        "{$this->_tagJoin}" .
                        "where " .
                        " artlang.online = 1 " .
                        " and artlang.idlang = :idlang " .
                        " {$useOfStartArticle} " .
                        " {$whereInCategories} " .
                        " {$where} " .
                        "group by " .
                        " artlang.idartlang " .
                        "{$this->_havingClause}" .
                        "{$orderBy} " .
                        "limit {$offset}, {$limit} " .
                        "", array(
                    ':idlang' => $this->_idlang,
                    ':tags' => count($this->_tags)
                ));

        if (!$results) {
            return;
        }

        $rows = array();
        foreach ($results as $result) {
            $tmpResult = array();
            if (isset($rows[$result['idartlang']])) {
                foreach ($result as $key => $value) {
                    if (substr($key, 0, 2) == 'f_') {
                        $fieldname = strtok($fieldname = substr($key, 2), '_');
                        $alias = strtok("\n");
                        $tmpResult[$alias][$result['f_filename_' . $alias]][$fieldname] = $value;
                    }
                }
                if (count($tmpResult) > 0) {
                    foreach ($tmpResult as $alias => $file) {
                        foreach ($file as $filename => $value) {
                            $rows[$result['idartlang']]->{
                                    $alias }
                                    [$filename][$result['f_mediaid_' . $alias]] = $value;
                        }
                    }
                }
            } else {
                foreach ($result as $key => $value) {
                    if (substr($key, 0, 2) != 'f_') {
                        $tmpResult[$key] = $value;
                    } else {
                        $fieldname = strtok(substr($key, 2), '_');
                        $alias = strtok("\n");
                        $tmpResult[$alias][$result['f_filename_' . $alias]][$result['f_mediaid_' . $alias]][$fieldname] = $value;
                    }
                }
                $rows[$result['idartlang']] = (object) $tmpResult;
            }
        }

        foreach ($rows as $row) {
            $this->_results[] = $row;
        }

        return;
    }

}