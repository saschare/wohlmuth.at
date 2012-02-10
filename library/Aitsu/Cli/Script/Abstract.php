<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Aitsu_Cli_Script_Abstract {
	
	protected $_options;
	
	abstract protected function _main();
	
	public function execute($options) {
		
		$this->_options = $options;
		$this->_main();
	}
}