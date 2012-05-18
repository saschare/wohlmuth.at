<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Rewrite implements Aitsu_Rewrite_Interface {

	protected $_rule = array ();

	protected function __construct() {

		$rules = Aitsu_Config :: get('rewrite.rule');

		if (empty ($rules)) {
			throw new Exception('When using Aitsu_Rewrite the config must assign rewrite rules (rewrite.rule).');
		}

		$this->_rule = $rules->toArray();
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function register() {
		return true;
	}

	public function registerParams() {

		foreach ($this->_rule as $rule => $exec) {
			if ($exec) {
				$rule = call_user_func(array (
					$rule,
					'getInstance'
				));
				if ($rule->register()) {
					$rule->registerParams();
				}
			}
		}
	}

	public function rewriteOutput($html) {

		foreach ($this->_rule as $rule => $exec) {
			if ($exec) {
				$rule = call_user_func(array (
					$rule,
					'getInstance'
				));
				$html = $rule->rewriteOutput($html);
			}
		}
		
		return $html;
	}
}