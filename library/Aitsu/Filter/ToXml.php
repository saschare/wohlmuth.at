<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: ToXml.php 18705 2010-09-10 17:25:12Z akm $}
 */

class Aitsu_Filter_ToXml implements Zend_Filter_Interface {

	public function filter($value) {

		$xml = new DOMDocument('1.0', 'utf-8');
		$root = $xml->createElement('root');
		$xml->appendChild($root);

		$this->_append($value, $root, $xml);

		return $xml;
	}

	protected function _append(& $value, $parentNode, $dom) {

		if (is_array($value)) {
			$this->_appendArray($value, $parentNode, $dom);
		}
		elseif (is_object($value)) {
			if (method_exists($value, 'toArray')) {
				$this->_appendArray($value->toArray(), $parentNode, $dom);
			} else {
				$this->_appendArray(get_object_vars($value), $parentNode, $dom);
			}
		}
	}

	protected function _appendArray(& $values, $parentNode, $dom) {

		$isAssoc = false;

		foreach ($values as $key => $value) {
			if (!is_numeric($key)) {
				$isAssoc = true;
			}
		}

		foreach ($values as $key => $value) {
			$name = $isAssoc ? $key : 'item';
			$node = $dom->createElement($name);
			$parentNode->appendChild($node);
			if (is_array($value) || is_object($value)) {
				$this->_append($value, $node, $dom);
			}
			elseif ($value != null && $value != '') {
				if (is_numeric($value) || preg_match('/^\\d{4}\\-\\d{2}\\-\\d{2}\\s\\d{2}\\:\\d{2}\\:\\d{2}$/', $value)) {
					$node->nodeValue = $value;
				} else {
					$cdata = $dom->createCDATASection((string) $value);
					$node->appendChild($cdata);
				}
			}
		}
	}

	public static function get($value) {

		$filter = new self();
		return $filter->filter($value);
	}
}