<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Security_Module_RenderOnly_Exception extends Aitsu_Security_Module_Exception {
	
	public function __construct($module, $code = 0) {
		
		$message = 'The module ' . $module . ' does not allow direct access.[Module: ' . $module . ']';
		
		parent :: __construct($message, 0);
	}
	
}