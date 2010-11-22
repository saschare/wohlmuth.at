<?php


/**
 * This class takes an iterator as an argument, iterates over its
 * entries and builds groups according to the conditions given.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Group.php 15947 2010-04-16 15:46:15Z akm $}
 */

class Aitsu_Filter_Group {

	protected $isGrouped = false;

	protected $it = null;
	protected $groups = array ();

	protected function __construct($it) {

		$this->it = $it;
	}

	public function factory($it) {

		return new self($it);
	}

	public function __set($key, $value) {

		if (!is_array($value) && !($value instanceof Aitsu_Filter_Condition_Interface)) {
			throw new Aitsu_Filter_Exception('Please provide either an array with ' .
			'a group name and a condition object or a condition object only.');
		}

		if (is_array($value) && !($value[1] instanceof Aitsu_Filter_Condition_Interface)) {
			throw new Aitsu_Filter_Exception('The filter condition must implement Aitsu_Filter_Condition_Interface');
		}

		if (is_a($value, 'Aitsu_Filter_Condition_Interface')) {
			$this->groups[$key] = (object) array (
				'name' => $key,
				'condition' => $value,
				'data' => array ()
			);
		} else {
			$this->groups[$key] = (object) array (
				'name' => $value[0],
				'condition' => $value[1],
				'data' => array ()
			);
		}
	}

	public function __get($key) {

		if (!$this->isGrouped) {
			$this->_populateGroups();
		}

		if (!isset ($this->groups[$key])) {
			throw new Aitsu_Filter_Exception('There is no group with the given name: ' . $key);
		}

		return $this->groups[$key]->data;
	}

	public function getGroups() {

		if (!$this->isGrouped) {
			$this->_populateGroups();
		}

		return $this->groups;
	}

	protected function _populateGroups() {

		/*
		 * We set isGrouped to true to prevent the iterator from being
		 * interated again and again and again.
		 */
		$this->isGrouped = true;

		foreach ($this->it as $entry) {
			foreach ($this->groups as $group) {
				if ($group->condition->isTrue($entry)) {
					$group->data[] = $entry;
				}
			}
		}
	}
}