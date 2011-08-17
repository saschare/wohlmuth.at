<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Wdrei_Interbrain_Perfect2_OrderPosition extends Wdrei_Interbrain_Perfect2_AbstractType {
	
	public $ItemId = '';
	/*public $Price = 10;
	public $TaxAmount = 0;
	public $ErrorCode = 0;*/

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