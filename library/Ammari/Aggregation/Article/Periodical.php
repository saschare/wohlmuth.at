<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Ammari_Aggregation_Article_Periodical extends Aitsu_Aggregation_Article {

	public function fetch($offset = 0, $limit = 100) {

		$this->_fetchResults($offset, $limit);

		$this->_evalRules();

		return $this;
	}

	protected function _evalRules() {

		/*
		 * Rule syntax: RuleName params
		 * Rule regex: ^(\w*)\s*(.*)$
		 * where \1 represents the rule and \2 its parameters. 
		 */

		if (count($this->results) == 0) {
			return;
		}

		$keys = array_keys($this->results);
		$row = $this->results[$keys[0]];

		if (!isset ($row->rule)) {
			throw new Ammari_Aggregation_Rule_Exception('The rule is missing.');
		}

		if (!isset ($row->ruleStart)) {
			throw new Ammari_Aggregation_Rule_Exception('The rule start (date) is missing.');
		}

		if (!isset ($row->ruleEnd)) {
			throw new Ammari_Aggregation_Rule_Exception('The rule end (date) is missing.');
		}

		$rows = $this->results;

		foreach ($rows as $index => $row) {
			if (!empty ($row->rule)) {
				if (!preg_match('/^(\\w*)\\s*(.*)$/', $row->rule, $match)) {
					if (Aitsu_Application_Status :: isPreview() || Aitsu_Application_Status :: isEdit()) {
						throw new Ammari_Aggregation_Rule_Syntax_Exception('The syntax of the rule is wrong.');
					} else {
						break;
					}
				} else {
					$ruleClass = $match[1];
					$dateTimes = call_user_func_array(array (
						'Ammari_Aggregation_Rule_' . ucfirst(strtolower($ruleClass)),
						'getDateTimes'
					), $row->ruleStart, $row->ruleEnd, $match[2]);
					$this->_replicate($index, $dateTimes);
				}
			}
		}
	}

	protected function _replicate($index, $dateTimes) {

		/*
		 * Do whatever seems appropriate.
		 */
	}

}