<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Module_SchemaOrg_Container extends Aitsu_Module_Container {

	protected $_index = null;
	protected $_type = '';
	protected $_params = array ();
	protected $_indexes = array ();
	protected $_pos = 0;

	protected function __construct() {
	}

	public static function factory($index, $type, $context, $indexes, $params = null) {

		$instance = parent :: factory($index, $type, $context, $indexes, $params);

		$instance->_type = 'Schema.Org.' . $type;

		return $instance;
	}

}