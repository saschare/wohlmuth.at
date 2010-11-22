<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Project.php 19209 2010-10-06 20:52:33Z akm $}
 */

class Plugin_Tracking_Project extends Aitsu_Persistence_Abstract {

	protected $_id = null;
	protected $_data = null;

	protected function __construct($id) {

		$this->_id = $id;
	}

	public function factory($id = null) {

		static $instance = array ();

		if ($id == null || !isset ($instance[$id])) {
			$instance = new self($id);
		}

		return $instance;
	}

	public function load() {

		if ($this->_id == null || $this->_data !== null) {
			return $this;
		}

		$this->_data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	project.*, ' .
		'	customer.customerid, ' .
		'	customer.identifier as customer ' .
		'from _tracking_project as project ' .
		'left join _tracking_customer as customer on project.customerid = customer.customerid ' .
		'where project.projectid = :id', array (
			':id' => $this->_id
		));

		return $this;
	}

	public function __get($key) {

		if ($this->_data === null) {
			$this->load();
		}

		if (!isset ($this->_data[$key])) {
			return null;
		}

		return stripslashes($this->_data[$key]);
	}

	public function __set($key, $value) {

		if ($this->_data === null) {
			$this->_data = array ();
		}

		$this->_data[$key] = $value;
	}

	public function save() {

		if (empty ($this->_data)) {
			return $this;
		}

		$this->_id = Aitsu_Db :: put('_tracking_project', 'projectid', $this->_data);

		Aitsu_Db :: query('update _tracking_project set enddate = null where enddate = :null', array (
			':null' => '0000-00-00'
		));

		return $this;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _tracking_project where projectid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getByIdentifier($identifier = '%', $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	project.*, ' .
		'	customer.customerid, ' .
		'	customer.identifier as customer ' .
		'from _tracking_project as project ' .
		'left join _tracking_customer as customer on project.customerid = customer.customerid ' .
		'where ' .
		'	project.identifier like :identifier ' .
		'	or customer.identifier like :identifier ' .
		'order by ' .
		'	identifier asc', array (
			':identifier' => $identifier
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['projectid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

	public static function getCurrentByIdentifier($identifier = '%', $limit = 100, $offset = 0) {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	project.*, ' .
		'	customer.customerid, ' .
		'	customer.identifier as customer ' .
		'from _tracking_project as project ' .
		'left join _tracking_customer as customer on project.customerid = customer.customerid ' .
		'where ' .
		'	( ' .
		'		project.identifier like :identifier ' .
		'		or customer.identifier like :identifier ' .
		'	) ' .
		'	and ( ' .
		'		project.enddate is null ' .
		'		or project.enddate > now() ' .
		'	) ' .
		'order by ' .
		'	identifier asc', array (
			':identifier' => $identifier
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['projectid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

}