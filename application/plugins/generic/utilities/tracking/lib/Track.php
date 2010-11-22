<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Track.php 19209 2010-10-06 20:52:33Z akm $}
 */

class Plugin_Tracking_Track extends Aitsu_Persistence_Abstract {

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
		'	track.*, ' .
		'	project.identifier as project ' .
		'from _tracking_track as track ' .
		'left join _tracking_project as project on track.projectid = project.projectid ' .
		'where track.trackid = :id', array (
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

		if (!isset ($this->_data['userid'])) {
			$this->_data['userid'] = Aitsu_Adm_User :: getInstance()->getId();
		}

		$this->_id = Aitsu_Db :: put('_tracking_track', 'trackid', $this->_data);

		return $this;
	}

	public function remove() {

		Aitsu_Db :: query('delete from _tracking_track where trackid = :id', array (
			':id' => $this->_id
		));
	}

	public static function getCurrent($term = '%') {

		$return = array ();

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	track.*, ' .
		'	project.identifier as project, ' .
		'	customer.identifier as customer, ' .
		'	ttype.identifier as tracktype ' .
		'from _tracking_track as track ' .
		'left join _tracking_project as project on track.projectid = project.projectid ' .
		'left join _tracking_customer as customer on project.customerid = customer.customerid ' .
		'left join _tracking_type as ttype on track.typeid = ttype.typeid ' .
		'where ' .
		'	track.closed is null ' .
		'	and ( ' .
		'		track.title like :term ' .
		'		or customer.identifier like :term ' .
		'		or project.identifier like :term ' .
		'	) ' .
		'order by ' .
		'	track.title asc', array (
			':term' => $term
		));

		if (!$results) {
			return $return;
		}

		foreach ($results as $result) {
			$row = new self($result['trackid']);
			$row->_data = $result;
			$return[] = $row;
		}

		return $return;
	}

}