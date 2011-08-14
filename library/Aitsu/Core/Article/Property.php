<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Core_Article_Property {

	protected $idartlang = null;
	protected $data = array ();
	protected $changed = false;

	protected function __construct($idartlang) {

		$this->idartlang = $idartlang;
		$this->_read();
	}

	public static function factory($idartlang = null) {

		static $instance = array ();

		$idartlang = $idartlang == null ? Aitsu_Registry :: get()->env->idartlang : $idartlang;

		if (!isset ($instance[$idartlang])) {
			$instance[$idartlang] = new self($idartlang);
		}

		return $instance[$idartlang];
	}

	protected function _read() {

		$properties = Aitsu_Db :: fetchAllC(60 * 60, '' .
		'select distinct ' .
		'	ptype.identifier, ' .
		'	ptype.type, ' .
		'	prop.textvalue, ' .
		'	prop.floatvalue, ' .
		'	prop.datevalue ' .
		'from _aitsu_article_property as prop ' .
		'left join _aitsu_property as ptype on prop.propertyid = ptype.propertyid ' .
		'where ' .
		'	prop.idartlang = ? ' .
		'	and ptype.propertyid is not null ' .
		'order by ' .
		'	ptype.identifier asc ', array (
			$this->idartlang
		));

		if (!$properties) {
			return;
		}

		foreach ($properties as $property) {
			$nameSpace = strtok($property['identifier'], ':');
			$name = strtok(':');
			$type = $property['type'] == 'serialized' ? 'text' : $property['type'];
			$value = $property[$type . 'value'];
			$this->data[$nameSpace][$name] = (object) array (
				'value' => $property['type'] == 'serialized' ? unserialize($value) : $value,
				'type' => $property['type']
			);
		}
	}

	public function getData() {

		return $this->data;
	}

	public function setValue($nameSpace, $token, $value, $type = 'text') {
		
		$this->_normalizeName($nameSpace);

		$this->changed = true;

		if ($type == 'float') {
			$value = str_replace(',', '.', $value);
		}

		$this->data[$nameSpace][$token] = (object) array (
			'value' => $value,
			'type' => $type
		);

	}

	public function unsetValue($nameSpace, $token) {
		
		$this->_normalizeName($nameSpace);

		if (isset ($this->data[$nameSpace][$token])) {
			$this->changed = true;
			unset ($this->data[$nameSpace][$token]);
		}
	}

	public function __destruct() {
		try {
			$this->_saveData();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	protected function _saveData() {

		if (!$this->changed || empty($this->idartlang)) {
			return;
		}

		try {
			Aitsu_Db :: startTransaction();
			
			$properties = array ();
			$results = Aitsu_Db :: fetchAll('select * from _aitsu_property');
			if ($results) {
				foreach ($results as $result) {
					$properties[$result['identifier']] = array (
						'type' => $result['type'],
						'id' => $result['propertyid']
					);
				}
			}

			Aitsu_Db :: query('delete from _aitsu_article_property where idartlang = :idartlang', array (
				':idartlang' => $this->idartlang
			));

			foreach ($this->data as $nameSpace => $tokens) {
				foreach ($tokens as $token => $value) {
					if (!array_key_exists($nameSpace . ':' . $token, $properties)) {
						/*
						 * Add entry in properties table.
						 */
						$insertId = Aitsu_Db :: query("" .
						"insert into _aitsu_property " .
						"(identifier, type) " .
						"values " .
						"(?, ?) ", array (
							$nameSpace . ':' . $token,
							$value->type
						))->getLastInsertId();
						$properties[$nameSpace . ':' . $token] = array (
							'type' => $value->type,
							'id' => $insertId
						);
					}

					if ($properties[$nameSpace . ':' . $token]['type'] != $value->type) {
						/*
						 * Type seems to have changed.
						 */
						Aitsu_Db :: query("" .
						"update _aitsu_property set type = ? " .
						"where identifier = ? ", array (
							$value->type,
							$nameSpace . ':' . $token
						));
						$properties[$nameSpace . ':' . $token]['type'] = $value->type;
					}

					/*
					 * Add value to the properties table.
					 */
					$type = $value->type == 'serialized' ? 'text' : $value->type;
					Aitsu_Db :: query("" .
					"insert into _aitsu_article_property " .
					"(propertyid, idartlang, {$type}value) " .
					"values " .
					"(?, ?, ?) " .
					"", array (
						$properties[$nameSpace . ':' . $token]['id'],
						$this->idartlang,
						$value->type == 'serialized' ? serialize($value->value) : $value->value
					));
				}
			}
			
			Aitsu_Db :: commit();
			
			Aitsu_Event :: raise('article.property.save.end', (object) array (
				'idartlang' => $this->idartlang,
				'action' => 'save'
			));
			
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}
	}

	public function getValue($nameSpace, $name) {
		
		$this->_normalizeName($nameSpace);

		if (!isset ($this->data[$nameSpace][$name])) {
			return null;
		}

		return $this->data[$nameSpace][$name];
	}

	public function getNamespace($nameSpace) {
		
		$this->_normalizeName($nameSpace);

		if (!isset ($this->data[$nameSpace])) {
			return array ();
		}

		return $this->data[$nameSpace];
	}
	
	protected function _normalizeName(& $name) {
		
		$name = preg_replace('/[^a-zA-Z_0-9]/', '_', $name);
		
		if (strlen($name) > 127) {
			$hash = hash('md4', $name);
			$name = substr($name, 0, 95) . $hash;
		}
	}
}