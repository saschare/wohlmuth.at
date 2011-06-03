<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Resourceunique.php 18581 2010-09-08 08:27:37Z akm $}
 */

class Aitsu_Validate_Resourceunique extends Zend_Validate_Abstract {

	const PRIVILEGEUNIQUE = 'resourceunique';
	protected $_id = null;

	protected $_messageTemplates = array (
		self :: PRIVILEGEUNIQUE => "'%value%' is not unique"
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
				'select count(*) from _acl_resource where name = :name ', array (
					':name' => $value
				)) > 0) {
				return false;
			}
		}

		if (Aitsu_Db :: fetchOne('' .
			'select count(*) from _acl_resource ' .
			'where ' .
			'	name = :name ' .
			'	and resourceid != :resourceid ', array (
				':name' => $value,
				':resourceid' => $this->_id
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
