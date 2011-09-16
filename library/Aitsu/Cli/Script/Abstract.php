<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
abstract class Aitsu_Cli_Script_Abstract {
	
	abstract protected function _main();
	
	public function execute() {
		
		$this->_main();
	}
}