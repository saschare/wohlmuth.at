<?php


/**
 * BreadCrumb navigation.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: BreadCrumb.php 16104 2010-04-23 09:13:12Z akm $}
 */

class Aitsu_Core_Navigation_BreadCrumb {

	protected $breadCrumb = null;

	protected function __construct() {
	}

	public static function get() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		if ($instance->breadCrumb == null) {
			$instance->_fetch();
		}

		return $instance->breadCrumb;
	}

	protected function _fetch() {

		$idlang = Aitsu_Registry :: get()->env->idlang;
		$idcat = Aitsu_Registry :: get()->env->idcat;

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	node.idcat, ' .
		'	catlang.name ' .
		'from _cat as child, _cat as node, _cat_lang as catlang ' .
		'where ' .
		'	child.lft between node.lft and node.rgt ' .
		'	and node.idcat = catlang.idcat ' .
		'	and catlang.idlang = ? ' .
		'	and child.idcat = ? ' .
		'order by ' .
		'	node.lft asc', array (
			$idlang,
			$idcat
		));

		if (!$results) {
			return;
		}

		$this->breadCrumb = array ();

		foreach ($results as $result) {
			$this->breadCrumb[] = (object) $result;
		}
	}
}