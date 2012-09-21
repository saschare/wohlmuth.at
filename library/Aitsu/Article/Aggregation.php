<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * @deprecated 2.4.3-44 - 2012-02-17
 */
class Aitsu_Article_Aggregation implements Iterator, Countable {

	protected $idlang;
	protected $types;

	protected $fetches = array ();
	protected $properties = array ();
	protected $media = array ();
	protected $whereInCategories = null;
	protected $whereBeneathCategory = null;
	protected $orderBy = null;
	protected $startArticle = 1;
	protected $filters = null;

	protected $offset = 0;
	protected $limit = 200;

	protected $results = array ();
	protected $position = 0;

	protected function __construct() {

		trigger_error('This class is deprecated. Please use Aitsu_Aggregation_Article instead.');

		$this->idlang = Aitsu_Registry :: get()->env->idlang;
		$this->whereInCategories = array (
			Aitsu_Registry :: get()->env->idcat
		);
	}

	public function factory() {

		$instance = new self();
		return $instance;
	}

	public function populateWith($type, $alias, $datatype = 'textvalue') {

		$firstPart = strtok($type, ':');
		$secondPart = strtok("\n");

		if ($firstPart == 'property') {
			$this->properties[$alias] = (object) array (
				'alias' => $secondPart,
				'type' => $datatype
			);
			return $this;
		}

		if ($firstPart == 'files') {
			$this->media[$alias] = (object) array (
				'filter' => str_replace('*', '%', $secondPart)
			);
			return $this;
		}

		$this->fetches[$alias] = (object) array (
			'name' => $type
		);

		return $this;
	}

	public function whereInCategories($categories) {

		$this->whereInCategories = $categories;

		return $this;
	}

	public function whereBeneathCategory($category) {

		$this->whereInCategories = Aitsu_Db :: fetchCol('' .
		'select distinct child.idcat from ' .
		'_cat as parent, ' .
		'_cat as child ' .
		'where ' .
		'	child.lft between parent.lft and parent.rgt ' .
		'	and parent.idcat = :idcat ', array (
			':idcat' => $category
		));

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

		return $this;
	}

	public function current() {

		return $this->results[$this->position];
	}

	public function key() {

		return $this->position;
	}

	public function next() {

		$this->position++;

		return $this;
	}

	public function valid() {

		return $this->position < count($this->results);
	}

	public function count() {

		return count($this->results);
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
		if ($this->fetches != null || $this->properties != null || $this->media != null) {
			$join = array ();
			$select = array ();
			if ($this->fetches != null) {
				foreach ($this->fetches as $alias => $type) {
					$join[] = "left join _article_content as tbl{$alias} on artlang.idartlang = tbl{$alias}.idartlang and tbl{$alias}.index = '{$type->name}' ";
					$select[] = "tbl{$alias}.value as {$alias} ";
				}
			}
			if ($this->properties != null) {
				foreach ($this->properties as $alias => $property) {
					$join[] = "left join _aitsu_property as spro{$alias} on spro{$alias}.identifier = '{$property->alias}' left join _aitsu_article_property as tbl{$alias} on tbl{$alias}.idartlang = artlang.idartlang and spro{$alias}.propertyid = tbl{$alias}.propertyid ";
					$select[] = "tbl{$alias}.{$property->type} as {$alias} ";
				}
			}
			if ($this->media != null) {
				foreach ($this->media as $alias => $filter) {
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

		if ($this->media != null) {
			$orderBy = $orderBy == '' ? $orderBy : $orderBy . ', ';
			$orderByAddOns = array ();
			foreach ($this->media as $alias => $value) {
				$orderByAddOns[] = "tbl{$alias}.mediaid desc";
			}
			$orderBy .= implode(', ', $orderByAddOns);
		}

		$results = Aitsu_Db :: fetchAll("" .
		"select straight_join distinct " .
		"	artlang.idart as idart, " .
		"	artlang.idartlang as idartlang, " .
		"	artlang.title as articletitle, " .
		"	artlang.pagetitle as pagetitle, " .
		"	artlang.teasertitle as teasertitle, " .
		"	artlang.summary as summary, " .
		"	artlang.created as created, " .
		"	unix_timestamp(artlang.created) as ts_created, " .
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

		$rows = array ();
		foreach ($results as $result) {
			$tmpResult = array ();
			if (isset ($rows[$result['idartlang']])) {
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
							$rows[$result['idartlang']]-> {
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
			$this->results[] = $row;
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
			$this->filters[] = str_replace('?', "tbl{$alias}.{$this->properties[$alias]->type}", $filter);
		}

		return $this;
	}

	public function remove($field, $value) {

		foreach ($this->results as $key => $val) {
			if ($val-> $field == $value) {
				unset ($this->results[$key]);
			}
		}

		$this->results = array_values($this->results);
	}
}