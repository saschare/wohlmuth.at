<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Generic.php 19858 2010-11-12 14:37:41Z akm $}
 */

class Aitsu_Persistence_Generic extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_table = null;
	protected $_data = null;
	protected $_columns = array ();
	protected $_backreference = array ();
	protected $_primaryKey = array ();

	protected function __construct($id) {

		$this->_id = $id;
	}

	public function factory($id = null, $table = null) {

		static $instance = array ();

		$instanceId = is_array($id) ? serialize($id) : $id;

		if (is_array($id)) {
			$instanceId = serialize($id);
		}
		elseif ($id == null) {
			$instanceId = uniqid();
		} else {
			$instanceId = $id;
		}

		if ($id == null || !isset ($instance[$instanceId])) {
			$instance[$instanceId] = new self($id);
		}

		if (is_null($table)) {
			throw new Exception('The paramter table must not be null.');
		}

		$instance[$instanceId]->_table = Aitsu_Db :: getInstance()->prefix($table);

		$instance[$instanceId]->_inspectTable();

		return $instance[$instanceId];
	}

	public function load($reload = false) {

		if (!$reload && ($this->_id == null || $this->_data !== null)) {
			return $this;
		}

		if (is_array($this->_id)) {
			$clause = array ();
			$data = array ();
			foreach ($this->_id as $columnName => $columnValue) {
				$clause[] = 'orig.' . $columnName . ' = :' . $columnName;
				$data[':' . $columnName] = $columnValue;
			}
			$clause = implode(' and ', $clause);
		} else {
			$clause = 'orig.' . $this->_primaryKey[0] . ' = :pk';
			$data = array (
				':pk' => $this->_id
			);
		}

		$counter = 0;
		$joins = array ();
		$aliases = array ();

		foreach ($this->_columns as $name => $column) {
			$aliases[] = 'orig.' . $name . ' as `' . $this->_table . '.' . $name . '`';
		}

		foreach ($this->_columns as $name => $column) {
			if (!empty ($column->referencedTable)) {
				$counter++;
				$join = 'left join ' . $column->referencedTable . ' as table' . $counter;
				$join .= ' on orig.' . $name . ' = table' . $counter . '.' . $column->referencedColumn;
				$joins[] = $join;
				foreach ($column->referencedTableColumns as $rcol) {
					$aliases[] = 'table' . $counter . '.' . $rcol . ' as `' . $column->referencedTable . '.' . $rcol . '`';
				}
			}
		}
		$joins = implode(' ', $joins);

		$aliases = implode(', ', $aliases);

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select ' . $aliases . ' from ' . $this->_table . ' as orig ' .
		$joins . ' ' .
		'where ' . $clause, $data, true);

		return $this;
	}

	public function __get($key) {

		if ($this->_data === null) {
			$this->load();
		}

		if (!isset ($this->_data[$key])) {
			return null;
		}

		return $this->_data[$key];
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function getData() {

		return $this->_data;
	}

	public function save() {

		if (empty ($this->_data)) {
			return;
		}

		// TODO: implement save.

		return $this;
	}

	public function remove() {

		// TODO: implement remove.

		return $this;
	}

	protected function _inspectTable() {

		$schema = Aitsu_Registry :: get()->config->database->params->dbname;

		$columns = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	column_name, ' .
		'	is_nullable, ' .
		'	data_type, ' .
		'	character_maximum_length, ' .
		'	column_key ' .
		'from information_schema.columns ' .
		'where ' .
		'	table_name = :tableName ' .
		'	and table_schema = :tableSchema ' .
		'order by ' .
		'	ordinal_position', array (
			':tableName' => $this->_table,
			':tableSchema' => $schema
		));

		foreach ($columns as $col) {
			$this->_columns[$col['column_name']] = (object) array (
				'null' => $col['is_nullable'] == 'YES' ? true : false,
				'type' => $col['data_type'],
				'length' => $col['character_maximum_length'],
				'primarykey' => $col['column_key'] == 'PRI' ? true : false
			);
			if ($col['column_key'] == 'PRI') {
				$this->_primaryKey[] = $col['column_name'];
			}
		}

		$constraints = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	column_name, ' .
		'	referenced_table_name, ' .
		'	referenced_column_name ' .
		'from information_schema.key_column_usage ' .
		'where ' .
		'	table_name = :tableName ' .
		'	and table_schema = :tableSchema', array (
			':tableName' => $this->_table,
			':tableSchema' => $schema
		));
		if ($constraints) {
			foreach ($constraints as $constraint) {
				$col = $this->_columns[$constraint['column_name']];
				$col->referencedTable = $constraint['referenced_table_name'];
				$col->referencedColumn = $constraint['referenced_column_name'];
				$col->referencedTableColumns = Aitsu_Db :: fetchCol('' .
				'select column_name from information_schema.columns ' .
				'where ' .
				'	table_name = :tableName ' .
				'	and table_schema = :tableSchema', array (
					':tableName' => $col->referencedTable,
					':tableSchema' => $schema
				));
				$this->_backreference[$constraint['referenced_table_name']] = $constraint['column_name'];
			}
		}
	}

	public function getFiltered(array $filter, $limit = 100, $offset = 0) {

		$counter = 0;
		$joins = array ();
		$aliases = array ();

		foreach ($this->_columns as $name => $column) {
			$aliases[] = 'orig.' . $name . ' as `' . $this->_table . '.' . $name . '`';
		}

		foreach ($this->_columns as $name => $column) {
			if (!empty ($column->referencedTable)) {
				$counter++;
				$join = 'left join ' . $column->referencedTable . ' as table' . $counter;
				$join .= ' on orig.' . $name . ' = table' . $counter . '.' . $column->referencedColumn;
				$joins[] = $join;
				foreach ($column->referencedTableColumns as $rcol) {
					$aliases[] = 'table' . $counter . '.' . $rcol . ' as `' . $column->referencedTable . '.' . $rcol . '`';
				}
			}
		}
		$joins = implode(' ', $joins);

		$aliases = implode(', ', $aliases);

		$clause = '1';
		$data = null;

		$data = Aitsu_Db :: fetchAll('' .
		'select ' . $aliases . ' from ' . $this->_table . ' as orig ' .
		$joins . ' ' .
		'where ' . $clause . ' ' .
		'limit ' . $offset . ', ' . $limit, $data, true);

		return $this->_aggregate($data);
	}

	protected function _aggregate($data) {

		if (empty ($data)) {
			return $data;
		}

		$return = array ();

		$keyMap = array ();
		foreach ($data[0] as $key => $value) {
			$entry = (object) array (
				'table' => strtok($key, '.'),
				'column' => strtok("\n")
			);

			if ($entry->table == $this->_table) {
				if ($this->_columns[$entry->column]->primarykey) {
					$type = (object) array (
						'type' => 'primarykey',
						'reference' => null
					);
					if (isset ($this->_columns[$entry->column]->referencedTable)) {
						$type->reference = (object) array (
							'table' => $this->_columns[$entry->column]->referencedTable,
							'column' => $this->_columns[$entry->column]->referencedColumn
						);
					}
				}
				elseif (isset ($this->_columns[$entry->column]->referencedTable)) {
					$type = (object) array (
						'type' => 'foreignkey',
						'reference' => (object) array (
							'table' => $this->_columns[$entry->column]->referencedTable,
							'column' => $this->_columns[$entry->column]->referencedColumn
						)
					);
				} else {
					$type = (object) array (
						'type' => 'value',
						'reference' => null
					);
				}
			} else {
				$type = (object) array (
					'type' => 'reference',
					'reference' => (object) array (
						'table' => $this->_table,
						'column' => $this->_backreference[$entry->table]
					)
				);
			}
			$entry->type = $type;

			$keyMap[$key] = $entry;
		}

		foreach ($data as $row) {
			$fields = array();
			foreach ($row as $key => $value) {
				$fields[] = (object) array (
					'value' => $value,
					'meta' => $keyMap[$key]
				);
			}
			$return[] = $fields;
		}

		return $return;
	}
}