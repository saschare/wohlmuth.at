<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Moraso_Aggregation_Article implements Iterator, Countable {

    protected $_idlang;
    protected $_types;
    protected $_fetches = array();
    protected $_properties = array();
    protected $_media = array();
    protected $_whereInCategories = null;
    protected $_orderBy = null;
    protected $_startArticle = 1;
    protected $_filters = null;
    protected $_tags = array();
    protected $_havingClause = '';
    protected $_tagJoin = '';
    protected $_offset = 0;
    protected $_limit = 200;
    protected $_results = array();
    protected $_position = 0;

    protected function __construct() {

        $this->_idlang = Aitsu_Registry :: get()->env->idlang;
        $this->_whereInCategories = array(
            Aitsu_Registry :: get()->env->idcat
        );
    }

    public static function factory() {

        $instance = new self();
        return $instance;
    }

    public function populateWith($type, $alias, $datatype = 'textvalue') {

        $firstPart = strtok($type, ':');
        $secondPart = strtok("\n");

        if ($firstPart == 'property') {
            $this->_properties[$alias] = (object) array(
                        'alias' => $secondPart,
                        'type' => $datatype
            );
            return $this;
        }

        if ($firstPart == 'files') {
            $this->_media[$alias] = (object) array(
                        'filter' => str_replace('*', '%', $secondPart)
            );
            return $this;
        }

        $this->_fetches[$alias] = (object) array(
                    'name' => $type
        );

        return $this;
    }

    public function whereInCategories($categories) {

        $this->_whereInCategories = $categories;

        return $this;
    }

    public function whereBeneathCategory($category) {

        if (empty($category)) {
            return $this;
        }

        $this->_whereInCategories = Aitsu_Db :: fetchCol('' .
                        'select distinct child.idcat from ' .
                        '_cat as parent, ' .
                        '_cat as child ' .
                        'where ' .
                        '	child.lft between parent.lft and parent.rgt ' .
                        '	and parent.idcat = :idcat ', array(
                    ':idcat' => $category
                ));

        return $this;
    }

    public function orderBy($alias, $ascending = true) {

        $this->_orderBy[$alias] = $ascending;

        return $this;
    }

    public function useOfStartArticle($startArticle) {

        $this->_startArticle = $startArticle;

        return $this;
    }

    public function fetch($offset = 0, $limit = 100) {

        $this->_fetchResults($offset, $limit);

        return $this;
    }

    public function rewind() {

        $this->_position = 0;

        return $this;
    }

    public function current() {

        return $this->_results[$this->_position];
    }

    public function key() {

        return $this->_position;
    }

    public function next() {

        $this->_position++;

        return $this;
    }

    public function valid() {

        return $this->_position < count($this->_results);
    }

    public function count() {

        return count($this->_results);
    }

    protected function _fetchResults($offset, $limit) {

        if (count($this->_results) > 0 && $this->_offset == $offset && $this->_limit = $limit) {
            /*
             * Data has already been fetched.
             */
            return;
        }

        /*
         * select clause -> $select
         * and joins -> $joins
         */
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

        /*
         * where in category -> $_whereInCategories
         */
        if ($this->_whereInCategories != null) {
            $whereInCategories = 'and catart.idcat in (' . implode(', ', $this->_whereInCategories) . ') ';
        } else {
            $whereInCategories = '';
        }

        /*
         * Use of start article -> $useOfStartArticle
         * 1 = show all (startarticle and others)
         * 2 = do not show start articles
         * 3 = show only start articles
         */
        switch ($this->_startArticle) {
            case 2 :
                // $useOfStartArticle = ' and catlang.startidartlang != artlang.idartlang ';
                $useOfStartArticle = ' and (catlang.startidartlang != artlang.idartlang or catlang.startidartlang is null) ';
                break;
            case 3 :
                $useOfStartArticle = ' and catlang.startidartlang = artlang.idartlang ';
                break;
            default :
                $useOfStartArticle = ' ';
        }

        /*
         * Filters -> $_where
         */
        if ($this->_filters == null) {
            $where = '';
        } else {
            $where = ' and ' . implode(' and ', $this->_filters);
        }

        /*
         * order by clause -> $_orderBy
         */
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
                        "	artlang.online = 1 " .
                        "	and artlang.idlang = :idlang " .
                        "	{$useOfStartArticle} " .
                        "	{$whereInCategories} " .
                        "	{$where} " .
                        "group by " .
                        "	artlang.idartlang " .
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
                        $fieldname = substr($key, 2);
                        $fieldname = strtok($fieldname, '_');
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
                        $fieldname = substr($key, 2);
                        $fieldname = strtok($fieldname, '_');
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

    public function addFilter($filter, $alias = null) {

        if ($alias == null) {
            $this->_filters[] = $filter;
        }

        if (array_key_exists($alias, $this->_fetches)) {
            $this->_filters[] = str_replace('?', "tbl{$alias}.value", $filter);
        }

        if (array_key_exists($alias, $this->_properties)) {
            $this->_filters[] = str_replace('?', "tbl{$alias}.{$this->_properties[$alias]->type}", $filter);
        }

        return $this;
    }

    public function havingTags($tags) {

        if ($tags == null || !is_array($tags)) {
            return;
        }

        if (count($tags) == 0) {
            return;
        }

        $this->_tags = $tags;

        $this->_havingClause = ' having count(tag.tagid) = :tags ';
        $this->_tagJoin = ' left join _tag_art tagart on artlang.idart = tagart.idart ' .
                'left join _tag tag on tagart.tagid = tag.tagid ' .
                'and tag.tag in (\'' . implode("','", $tags) . '\') ';

        return $this;
    }

    public function remove($field, $value) {

        foreach ($this->_results as $key => $val) {
            if ($val->$field == $value) {
                unset($this->_results[$key]);
            }
        }

        $this->_results = array_values($this->_results);
    }

}