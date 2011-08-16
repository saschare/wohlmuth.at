<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Wdrei_Interbrain_Perfect2_ClientData extends Wdrei_Interbrain_Perfect2_AbstractType {
	
	public $Id = 1;
	public $Typ = 2;
	public $AllowsAdvertising = false;

	public static function instance($args = null) {
		
		$instance = new self();
		
		if (is_array($args)) {
			foreach ($args as $key => $value) {
				$instance->$key = $value;
			}
		}
		
		return $instance;
	} 
}