<?php


/**
 * The aim of the Zend_View extension is to provide a defined
 * subset of data bound to the view to be used in in the 
 * schema.org modules.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Module_SchemaOrg_View extends Zend_View implements Iterator {

	protected $_subSetOfPublicVars;

	public function __construct($config = array ()) {

		parent :: __construct($config);
		$this->_subSetOfPublicVars = (object) array (
			'members' => array (),
			'pos' => 0
		);
	}

	public function rewind() {

		$exclusions = array (
			'idart',
			'SchemaOrgType'
		);

		$vars = get_object_vars($this);

		foreach ($vars as $key => $value) {
			if (substr($key, 0, 1) != '_' && !in_array($key, $exclusions)) {
				$this->_subSetOfPublicVars->members[] = $key;
			}
		}

		$this->_subSetOfPublicVars->pos = 0;
	}

	public function current() {

		$var = $this->_subSetOfPublicVars->members[$this->_subSetOfPublicVars->pos];
		return $this-> $var;
	}

	public function key() {

		return $this->_subSetOfPublicVars->members[$this->_subSetOfPublicVars->pos];
	}

	public function next() {

		$this->_subSetOfPublicVars->pos++;
	}

	public function valid() {

		return count($this->_subSetOfPublicVars->members) > $this->_subSetOfPublicVars->pos;
	}
}