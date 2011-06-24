<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Db {

	static public $debugNext = false;

	protected $db;
	protected $prefix;
	protected $rowCount;
	protected $_publishMap = null;

	/**
	 * Constructor.
	 */
	protected function __construct() {

		$this->_initializeDbConnection();

		$this->db = Aitsu_Registry :: get()->db;
		$this->prefix = Aitsu_Registry :: get()->config->database->params->tblprefix;
		$this->prefix = $this->prefix == null ? '' : $this->prefix;
	}

	protected function _initializeDbConnection() {

		$db = Zend_Db :: factory(Aitsu_Registry :: get()->config->database);
		Aitsu_Registry :: get()->db = $db;
	}

	/**
	 * Singleton.
	 */
	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Redirects the query to the zend API.
	 * @param String Method to be used.
	 * @param String Query to be used.
	 * @param Array Values to be used within the query.
	 * @param Boolean Whether or not to suppress the table prefix replacement.
	 * @return Mixed Returns whatever zend API is returning.
	 */
	protected function _query($method, $query, $vars, $suppressTablePrefix, $showQuery, $cachingPeriod = null) {

		if (!is_null($cachingPeriod)) {
			/*
			 * Caching has to be used.
			 */
			$cacheId = hash('md4', var_export(array (
				$method,
				$query,
				$vars,
				$suppressTablePrefix
			), true));
			$cache = Aitsu_Cache :: getInstance($cacheId);
			if (Aitsu_Registry :: isEdit()) {
				$cache->remove();
			}
			if ($cache->isValid()) {
				return unserialize($cache->load());
			}
		}

		if (strlen(trim($query)) == 0) {
			return false;
		}

		$returnValue = null;

		$fQuery = $suppressTablePrefix ? $query : $this->prefix($query);

		$profileId = substr($fQuery, 0, 50);
		Aitsu_Profiler :: profile($profileId, null, 'db.query');

		if ($showQuery) {
			echo '<pre>';
			echo $fQuery;
			echo '</pre>';
		}

		if (self :: $debugNext) {
			trigger_error($fQuery);
			self :: $debugNext = false;
		}

		try {
			if ($vars != null) {
				$returnValue = $this->db-> $method ($fQuery, $vars);
			} else {
				$returnValue = $this->db-> $method ($fQuery);
			}
		} catch (Exception $e) {
			Aitsu_Event :: raise('exception.sql', array (
				'exception' => $e,
				'query' => $fQuery,
				'params' => $vars
			));
			throw $e;
		}

		Aitsu_Profiler :: profile($profileId, (object) array (
			'query' => $fQuery,
			'trace' => debug_backtrace()
		), 'db.query');

		if (!is_null($cachingPeriod)) {
			$cache->setLifetime($cachingPeriod == 'eternal' ? 365 * 24 * 60 * 60 : $cachingPeriod);
			$cache->save(serialize($returnValue), array (
				'db'
			));
		}

		return $returnValue;
	}

	/**
	 * Returns a indexed associative array with with the data returned by
	 * the specified query.
	 * @param String Query.
	 * @param Array Indexed array containing the values binded in the query.
	 * @param Boolean Whether or not the table prefix replacement has to be suppressed.
	 * @return Array Indexed associative array containing the result of the query.
	 */
	public static function fetchAll($query, $vars = null, $suppressTablePrefix = false, $showQuery = false, $cachingPeriod = null) {

		return self :: getInstance()->_query('fetchAll', $query, $vars, $suppressTablePrefix, $showQuery, $cachingPeriod);
	}

	public static function fetchAllC($period, $query, $vars = null) {

		return self :: getInstance()->_query('fetchAll', $query, $vars, false, false, $period);
	}

	public static function filter($baseQuery, $limit = null, $offset = null, $filters = null, $orders = null) {

		$limit = is_null($limit) || !is_numeric($limit) ? 100 : $limit;
		$offset = is_null($offset) || !is_numeric($offset) ? 0 : $offset;
		$filters = is_array($filters) ? $filters : array ();
		$orders = is_array($orders) ? $orders : array ();

		$filterClause = array ();
		$filterValues = array ();
		for ($i = 0; $i < count($filters); $i++) {
			$filterClause[] = $filters[$i]->clause . ' :value' . $i;
			$filterValues[':value' . $i] = $filters[$i]->value;
		}
		$where = count($filterClause) == 0 ? '' : 'where ' . implode(' and ', $filterClause);

		$orderBy = count($orders) == 0 ? '' : 'order by ' . implode(', ', $orders);

		$results = self :: fetchAll('' .
		$baseQuery .
		' ' . $where .
		' ' . $orderBy .
		'limit ' . $offset . ', ' . $limit, $filterValues);

		$return = array ();

		if ($results) {
			foreach ($results as $result) {
				$return[] = (object) $result;
			}
		}

		return $return;
	}

	/**
	 * Returns exactly one single field value. If the query return more than one row or more
	 * than one field, the method throws an exception.
	 * @param String Query.
	 * @param Array Indexed array containing the values binded in the query.
	 * @param Boolean Whether or not the table prefix replacement has to be suppressed.
	 * @return String Field value returned by the query.
	 */
	public static function fetchOne($query, $vars = null, $suppressTablePrefix = false, $showQuery = false, $cachingPeriod = null) {

		return self :: getInstance()->_query('fetchOne', $query, $vars, $suppressTablePrefix, $showQuery, $cachingPeriod);
	}

	public static function fetchOneC($period, $query, $vars = null) {

		return self :: getInstance()->_query('fetchOne', $query, $vars, false, false, $period);
	}

	/**
	 * Returns an associative array returning exactly one single record. If the query
	 * returns more than one row, the method throws an exception.
	 * @param String Query.
	 * @param Array Indexed array containing the values binded in the query.
	 * @param Boolean Whether or not the table prefix replacement has to be suppressed.
	 * @return Array Associative array containing a single record.
	 */
	public static function fetchRow($query, $vars = null, $suppressTablePrefix = false, $showQuery = false, $cachingPeriod = null) {

		return self :: getInstance()->_query('fetchRow', $query, $vars, $suppressTablePrefix, $showQuery, $cachingPeriod);
	}

	public static function fetchRowC($period, $query, $vars = null) {

		return self :: getInstance()->_query('fetchRow', $query, $vars, false, false, $period);
	}

	public static function fetchCol($query, $vars = null, $suppressTablePrefix = false, $showQuery = false, $cachingPeriod = null) {

		return self :: getInstance()->_query('fetchCol', $query, $vars, $suppressTablePrefix, $showQuery, $cachingPeriod);
	}

	public static function fetchColC($period, $query, $vars = null) {

		return self :: getInstance()->_query('fetchCol', $query, $vars, false, false, $period);
	}

	/**
	 * Runs the specified query on the database and returns a reference to the database
	 * object to allow to fetch further data.
	 * @param String Query.
	 * @param Array Indexed array containing the values binded in the query.
	 * @param Boolean Whether or not the table prefix replacement has to be suppressed.
	 * @return Object Reference to the singleton.
	 */
	public static function query($query, $vars = null, $suppressTablePrefix = false, $suppressExceptions = false, $showQuery = false) {

		self :: getInstance()->rowCount = self :: getInstance()->_query('query', $query, $vars, $suppressTablePrefix, $showQuery);
		return self :: getInstance();
	}

	/**
	 * Returns a reference to the database adapter.
	 * @return Object Database adapter.
	 */
	public static function getDb() {

		return self :: getInstance()->db;
	}

	/**
	 * Replaces the underscore of the table names with the appropriate table prefix.
	 * @param String Query.
	 * @return String Query.
	 */
	public static function prefix($query) {

		if (self :: $debugNext) {
			trigger_error(var_export(Aitsu_Application_Status :: isPreview(), true));
		}

		if (!Aitsu_Application_Status :: isPreview()) {
			$query = self :: getInstance()->_productionQuery($query);
		}

		$prefix = self :: getInstance()->prefix;

		return preg_replace('/([^a-zA-Z0-9\\.]|^)_/', "$1{$prefix}", $query);
	}

	protected function _productionQuery($query) {

		if (in_array(substr($query, 0, 6), array (
				'insert',
				'update',
				'delete',
				'create'
			))) {
			/*
			 * No rewriting is done, if it is a crud statement.
			 */
			return $query;
		}

		if (is_null($this->_publishMap)) {
			$this->_publishMap = new Zend_Config_Ini('application/configs/publishmap.ini');
		}

		foreach ($this->_publishMap as $type => $tables) {
			foreach ($tables->toArray() as $table) {
				$query = str_replace($table['source'], $table['view'], $query);
			}
		}

		return $query;
	}

	/**
	 * Returns the last inserted auto-increment value.
	 * @return Integer Last inserted auto-increment value.
	 */
	public function getLastInsertId() {

		return $this->_query('fetchOne', 'select last_insert_id()', null, true, false);
	}

	/**
	 * Replaces all occurences of carriage return or line feed to their ascii
	 * equivalents (\r, \n respectively).
	 * @param String Text to be escaped.
	 * @return String Escaped text.
	 */
	public static function escapeString($text) {

		$text = addslashes($text);
		$text = preg_replace('/\\n/', '\n', $text);
		$text = preg_replace('/\\r/', '\r', $text);

		return $text;
	}

	/**
	 * Returns the number of affected rows of the last insert, update or
	 * delete statement fired with the query method.
	 * @return Integer Number of affected rows.
	 */
	public function rowCount() {

		return self :: fetchOne('select row_count()');
	}

	public static function startTransaction() {

		self :: query('set autocommit = 0');
	}

	public static function commit() {

		self :: query('commit');
	}

	public static function rollback() {

		self :: query('rollback');
	}

	public static function put($table, $primarykey, array $data) {

		$fields = array ();
		$values = array ();
		$updates = array ();

		$columns = self :: fetchAll('show columns from ' . $table);

		foreach ($columns as $col) {
			if (isset ($data[$col['Field']])) {
				$fields[] = $col['Field'];
				$values[':' . $col['Field']] = $data[$col['Field']];
				$updates[] = $col['Field'] . ' = ' . ':' . $col['Field'];
			}
		}

		if ($primarykey != null && isset ($data[$primarykey])) {
			$values[':' . $primarykey] = $data[$primarykey];
			self :: query('update ' . $table . ' set ' . implode(', ', $updates) . ' where ' . $primarykey . ' = :' . $primarykey, $values);
			return $data[$primarykey];
		}

		return self :: query('insert into ' . $table . ' (`' . implode('`, `', $fields) . '`) values (:' . implode(', :', $fields) . ') ', $values)->getLastInsertId();
	}

	public static function debugNext() {

		self :: $debugNext = true;
	}
}