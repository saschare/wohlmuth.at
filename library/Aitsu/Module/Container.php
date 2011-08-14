<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Module_Container implements Iterator {

	protected $_index = null;
	protected $_type = '';
	protected $_context = null;
	protected $_params = array ();
	protected $_indexes = array ();
	protected $_pos = 0;

	protected function __construct() {
	}

	public static function factory($index, $type, $context, $indexes, $params = null) {

		$instance = new self();

		$instance->_type = $type;
		$instance->_context = $context;

		preg_match_all('/^\\s*(.*?)\\s*$/m', $indexes, $matches);
		for ($i = 0; $i < count($matches[0]); $i++) {
			$instance->_indexes[] = $index . '_' . $matches[1][$i];
		}

		if (is_string($params)) {
			$instance->_params['template'] = $params;
		}
		elseif (is_array($params)) {
			$instance->_params = $params;
		}
		
		return $instance;
	}

	public function __toString() {

		$out = '';

		foreach ($this->_indexes as $index) {
			$out .= "\n" . $this->_getScript($index);
		}
		
		return $out;
	}

	protected function _getScript($index) {

		$out = '<script type="application/x-aitsu" src="' . $this->_type . ':' . $index . '">' . "\n";
		if ($this->_context != null) {
			$out .= 'context = ' . $this->_context . "\n";
		}
		foreach ($this->_params as $key => $value) {
			$out .= $key . ' = ' . $value . "\n";
		}
		$out .= '</script>';
		
		return $out;
	}

	public function rewind() {
		
		$this->_pos = 0;
	}

	public function current() {
		
		return $this->_getScript($this->_indexes[$this->_pos]);
	}

	public function key() {
		
		return $this->_pos;
	}

	public function next() {
		
		$this->_pos++;
	}

	public function valid() {
		
		return count($this->_indexes) > $this->_pos;
	}
}