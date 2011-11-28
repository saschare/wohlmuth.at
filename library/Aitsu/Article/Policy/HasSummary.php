<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Article_Policy_HasSummary extends Aitsu_Article_Policy_Abstract {

	protected function _evalStatement($statement) {

		if (preg_match('/(\\d*)\\s*\\-\\s*(\\d*)/', $statement, $match)) {
			$min = $match[1];
			$max = $match[2];
		} else {
			$min = 0;
			$max = 4000;
		}

		return (object) array (
			'min' => $min,
			'max' => $max
		);
	}

	protected function _isFullfilled() {

		/*
		 * Following conditions have to be met to fullfill:
		 * 
		 * - the article must have a page title.
		 */

		$pageTitle = Aitsu_Db :: fetchOne('' .
		'select summary from _art_lang where idartlang = :idartlang', array (
			':idartlang' => $this->_idartlang
		));

		if (strlen(trim($pageTitle)) < $this->_statement->min) {
			$this->_message = 'length of summary < ' . $this->_statement->min;
			return false;
		}

		if (strlen(trim($pageTitle)) > $this->_statement->max) {
			$this->_message = 'length of summary > ' . $this->_statement->max;
			return false;
		}

		return true;
	}

}