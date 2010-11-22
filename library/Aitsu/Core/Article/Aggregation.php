<?php


/**
 * Article aggregation.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Aggregation.php 16692 2010-05-27 23:30:39Z akm $}
 * 
 * @deprecated 0.9.2 - 07.10.2010
 */

class Aitsu_Core_Article_Aggregation implements Iterator {

	protected $idlang;
	protected $types;

	protected $fetches = array();
	protected $properties = array();
	protected $whereInCategories = null;
	protected $whereBeneathCategory = null;
	protected $orderBy = null;
	protected $startArticle = 1;
	protected $filters = null;

	protected $offset = 0;
	protected $limit = 200;

	protected $results = array ();
	protected $position;

	protected function __construct() {

		$this->types = array (
			'htmlhead' => array (
				1,
				true
			),
			'html' => array (
				2,
				true
			),
			'text' => array (
				3,
				false
			),
			'img' => array (
				4,
				false
			),
			'imgdescr' => array (
				5,
				false
			),
			'link' => array (
				6,
				true
			),
			'head' => array (
				9,
				true
			)
		);

		$this->idlang = Aitsu_Registry :: get()->env->idlang;
		$this->whereInCategories = array(Aitsu_Registry :: get()->env->idcat);
	}

	public function factory() {

		$instance = new self();
		return $instance;
	}

	public function populateWith($type, $index, $alias, $datatype = 'textvalue') {

		if (!array_key_exists($type, $this->types)) {
			if (strtok($type, ':') == 'property') {
				$this->properties[$alias]['alias'] = strtok("\n");
				$this->properties[$alias]['type'] = $datatype;
			} else {
				/*
				 * Ignore the command, as the type does not exist.
				 */
			}
			return $this;
		}

		$this->fetches[$alias] = array (
			'idtype' => $this->types[$type][0],
			'typeid' => $index,
			'urlencoded' => $this->types[$type][1]
		);

		return $this;
	}

	public function whereInCategories($categories) {

		$this->whereInCategories = $categories;

		return $this;
	}

	public function whereBeneathCategory($category) {

		$this->whereBeneathCategory = $category;
		$this->whereInCategories = null;

		return $this;
	}

	public function orderBy($alias, $ascending = true) {

		$this->orderBy[$alias] = $ascending;

		return $this;
	}

	public function useOfStartArticle($startArticle) {

		$this->startArticle = $startArticle;

		return $this;
	}

	public function fetch($offset = 0, $limit = 100) {

		$this->_fetchResults($offset, $limit);

		return $this;
	}

	public function rewind() {

		$this->position = 0;
	}

	public function current() {

		return $this->results[$this->position];
	}

	public function key() {

		return $this->position;
	}

	public function next() {

		$this->position++;
	}

	public function valid() {

		return $this->position < count($this->results);
	}
	
	public function count() {
		
		return $this->results;
	}

	protected function _fetchResults($offset, $limit) {

		if (count($this->results) > 0 && $this->offset == $offset && $this->limit = $limit) {
			/*
			 * Data has already been fetched.
			 */
			return;
		}

		/*
		 * select clause -> $select
		 * and joins -> $joins
		 */
		if ($this->fetches != null || $this->properties != null) {
			$join = array ();
			$select = array ();
			if ($this->fetches != null) {
				foreach ($this->fetches as $alias => $type) {
					$join[] = "left join _content as tbl{$alias} on artlang.idartlang = tbl{$alias}.idartlang and tbl{$alias}.idtype = {$type['idtype']} and tbl{$alias}.typeid = {$type['typeid']} ";
					$select[] = "tbl{$alias}.value as {$alias} ";
				}
			}
			if ($this->properties != null) {
				foreach ($this->properties as $alias => $property) {
					$join[] = "left join (select stbl{$alias}.idartlang, stbl{$alias}.{$property['type']} from _aitsu_article_property as stbl{$alias} left join _aitsu_property as spro{$alias} on spro{$alias}.propertyid = stbl{$alias}.propertyid where spro{$alias}.identifier = '{$property['alias']}') as tbl{$alias} on tbl{$alias}.idartlang = artlang.idartlang ";
					$select[] = "tbl{$alias}.{$property['type']} as {$alias} ";
				}
			}
			$selects = ', ' . implode(', ', $select);
			$joins = implode('', $join);
		} else {
			$selects = '';
			$joins = '';
		}

		/*
		 * where in category -> $whereInCategories
		 */
		if ($this->whereInCategories != null) {
			$whereInCategories = 'and catart.idcat in (' . implode(', ', $this->whereInCategories) . ') ';
		} else {
			$whereInCategories = '';
		}

		/*
		 * Use of start article -> $useOfStartArticle
		 * 1 = show all (startarticle and others)
		 * 2 = do not show start articles
		 * 3 = show only start articles
		 */
		switch ($this->startArticle) {
			case 2 :
				$useOfStartArticle = ' and catlang.startidartlang != artlang.idartlang ';
				break;
			case 3 :
				$useOfStartArticle = ' and catlang.startidartlang = artlang.idartlang ';
				break;
			default :
				$useOfStartArticle = ' ';
		}
		
		/*
		 * Filters -> $where
		 */
		if ($this->filters == null) {
			$where = '';
		} else {
			$where = ' and ' . implode(' and ', $this->filters);
		}

		/*
		 * order by clause -> $orderBy
		 */
		$orderAlias['modified'] = 'artlang.lastmodified';
		$orderAlias['created'] = 'artlang.created';
		$orderAlias['artsort'] = 'artlang.artsort';
		if ($this->orderBy != null) {
			$orderBy = 'order by ';
			$order = array ();
			foreach ($this->orderBy as $alias => $ascending) {
				$order[] = $alias . ' ' . ($ascending ? 'asc' : 'desc');
			}
			$orderBy .= implode(', ', $order);
		} else {
			$orderBy = '';
		}

		$results = Aitsu_Db :: fetchAll("" .
		"select distinct " .
		"	artlang.idart as idart, " .
		"	artlang.idartlang as idartlang, " .
		"	artlang.title as articletitle, " .
		"	artlang.pagetitle as pagetitle, " .
		"	artlang.summary as summary, " .
		"	artlang.created as created, " .
		"	artlang.lastmodified as modified," .
		"	artlang.artsort as artsort " .
		"	{$selects} " .
		"from _art_lang as artlang " .
		"{$joins} " .
		"left join _cat_art as catart ON artlang.idart = catart.idart " .
		"left join _cat_lang as catlang ON catart.idcat = catlang.idcat AND catlang.idlang = ? " .
		"where " .
		"	artlang.online = 1 " .
		"	and artlang.idlang = ? " .
		"	{$useOfStartArticle} " .
		"	{$whereInCategories} " .
		"	{$where} " .
		"{$orderBy} " .
		"limit {$offset}, {$limit} " .
		"", array (
			$this->idlang,
			$this->idlang
		));

		if (!$results) {
			return;
		}

		foreach ($results as $result) {
			$entry = (object) $result;
			foreach ($result as $key => $value) {
				if (isset ($this->fetches[$key]) && $this->fetches[$key]['urlencoded']) {
					$entry-> $key = urldecode($value);
				}
				if (isset ($this->fetches[$key]['idtype']) && $this->fetches[$key]['idtype'] == 4) {
					$entry-> $key = Aitsu_Db :: fetchOne('select concat(dirname, filename) from _upl where idupl = ?', array($value));
				}
			}
			$this->results[] = $entry;
		}

		return;
	}

	public function addFilter($filter, $alias = null) {
		
		if ($alias == null) {
			$this->filters[] = $filter;
		}

		if (array_key_exists($alias, $this->fetches)) {
			$this->filters[] = str_replace('?', "tbl{$alias}.value", $filter);
		}
		
		if (array_key_exists($alias, $this->properties)) {
			$this->filters[] = str_replace('?', "tbl{$alias}.{$this->properties[$alias]['type']}", $filter);
		}
		
		return $this;
	}
}