<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Roleunique.php 18493 2010-09-01 11:29:58Z akm $}
 */

class Aitsu_Validate_Roleunique extends Zend_Validate_Abstract {

	const ROLEUNIQUE = 'roleunique';
	protected $_id = null;

	protected $_messageTemplates = array (
		self :: ROLEUNIQUE => "'%value%' is not unique"
	);

	public function isValid($value) {

		$this->_setValue($value);

		if (!$this->_isUnique($value)) {
			$this->_error();
			return false;
		}

		return true;
	}

	protected function _isUnique($value) {

		if ($this->_id == null) {
			/*
			 * No exclusion is made.
			 */
			if (Aitsu_Db :: fetchOne('' .
				'select count(*) from _acl_role where identifier = :identifier ', array (
					':identifier' => $value
				)) > 0) {
				return false;
			}
		}

		if (Aitsu_Db :: fetchOne('' .
			'select count(*) from _acl_role ' .
			'where ' .
			'	identifier = :identifier ' .
			'	and roleid != :roleid ', array (
				':identifier' => $value,
				':roleid' => $this->_id
			)) > 0) {
			return false;
		}

		return true;
	}

	public function setId($id) {

		$this->_id = $id;
	}

}
?>
