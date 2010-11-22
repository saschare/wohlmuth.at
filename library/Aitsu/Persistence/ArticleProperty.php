<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ArticleProperty.php 20000 2010-11-19 18:22:14Z akm $}
 */

class Aitsu_Persistence_ArticleProperty extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_idartlang = null;
	protected $_data = null;
	protected $_idlang = null;

	protected function __construct($id) {

		$this->_id = $id;
		$this->_idlang = Aitsu_Registry :: get()->session->currentLanguage;
	}

	public function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance[$id] = new self($id);
		}

		return $instance[$id];
	}

	public function load($reload = false) {

		if (!$reload && ($this->_id == null || $this->_data !== null)) {
			return $this;
		}

		$properties = Aitsu_Db :: fetchAll('' .
		'select distinct ' .
		'	ptype.identifier, ' .
		'	ptype.type, ' .
		'	prop.textvalue, ' .
		'	prop.floatvalue, ' .
		'	prop.datevalue ' .
		'from _aitsu_article_property as prop ' .
		'left join _aitsu_property as ptype on prop.propertyid = ptype.propertyid ' .
		'left join _art_lang as artlang on prop.idartlang = artlang.idartlang ' .
		'where ' .
		'	artlang.idartlang = :idartlang ' .
		'	and ptype.propertyid is not null ' .
		'order by ' .
		'	ptype.identifier asc ', array (
			':idartlang' => $this->_id
		));

		if (!$properties) {
			return;
		}

		foreach ($properties as $property) {
			$nameSpace = strtok($property['identifier'], ':');
			$name = strtok(':');
			$type = $property['type'] == 'serialized' ? 'text' : $property['type'];
			$value = $property[$type . 'value'];
			$this->_data[$nameSpace][$name] = (object) array (
				'value' => $property['type'] == 'serialized' ? unserialize($value) : $value,
				'type' => $property['type']
			);
		}

		return $this;
	}

	public function __get($key) {

		if ($this->_data === null) {
			$this->load();
		}

		if (!isset ($this->_data[$key])) {
			$this->_data[$key] = array ();
		}

		return $this->_data[$key];
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function __isset($key) {

		return isset ($this->_data[$key]);
	}

	public function save() {

		if (empty ($this->_data)) {
			return;
		}

		Aitsu_Db :: startTransaction();

		try {
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
				':idartlang' => $this->_id
			));

			foreach ($this->_data as $nameSpace => $tokens) {
				foreach ($tokens as $token => $value) {
					if (!array_key_exists($nameSpace . ':' . $token, $properties)) {
						/*
						 * Add entry in properties table.
						 */
						$insertId = Aitsu_Db :: query('' .
						'insert into _aitsu_property (identifier, type) ' .
						'values (:identifier, :type)', array (
							':identifier' => $nameSpace . ':' . $token,
							':type' => $value->type
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
						Aitsu_Db :: query('' .
						'update _aitsu_property set type = :type ' .
						'where identifier = :identifier', array (
							':type' => $value->type,
							':identifier' => $nameSpace . ':' . $token
						));
						$properties[$nameSpace . ':' . $token]['type'] = $value->type;
					}

					/*
					 * Add value to the properties table.
					 */
					$type = $value->type == 'serialized' ? 'text' : $value->type;
					Aitsu_Db :: query('' .
					'insert into _aitsu_article_property ' .
					'(propertyid, idartlang, ' . $type . 'value) ' .
					'values ' .
					'(:propertyid, :idartlang, :type)', array (
						'propertyid' => $properties[$nameSpace . ':' . $token]['id'],
						':idartlang' => $this->_id,
						':type' => $value->type == 'serialized' ? serialize($value->value) : $value->value
					));
				}
			}

			Aitsu_Persistence_Article :: touch($this->_id);

			Aitsu_Db :: commit();
		} catch (Exception $e) {
			Aitsu_Db :: rollback();
			throw $e;
		}

		return $this;
	}

	public function remove() {

		return $this;
	}

	public function unsetValue($nameSpace, $token) {

		if (isset ($this->data[$nameSpace][$token])) {
			unset ($this->data[$nameSpace][$token]);
		}
	}

	public function setValue($nameSpace, $token, $value, $type = 'text') {

		if ($type == 'float') {
			$value = str_replace(',', '.', $value);
		}

		$this->_data[$nameSpace][$token] = (object) array (
			'value' => $value,
			'type' => $type
		);
	}

}