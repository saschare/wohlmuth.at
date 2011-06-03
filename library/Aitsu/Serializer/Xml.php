<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id$}
 */

class Aitsu_Serializer_Xml {
	
	public function get($obj, $rootNode = 'root') {
		
		if (is_object($obj)) {
			return self :: _generateValidXmlFromObj($obj, $rootNode);
		} else {
			return self :: _generateValidXmlFromArray($obj, $rootNode);
		}
	}

	protected static function _generateValidXmlFromObj($obj, $node_block = 'nodes', $node_name = 'node') {
		
		$arr = get_object_vars($obj);
		
		return self :: _generateValidXmlFromArray($arr, $node_block, $node_name);
	}

	protected static function _generateValidXmlFromArray($array, $node_block = 'nodes', $node_name = 'node') {
		
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>';

		$xml .= '<' . $node_block . '>';
		$xml .= self :: _generateXmlFromArray($array, $node_name);
		$xml .= '</' . $node_block . '>';

		return $xml;
	}

	protected static function _generateXmlFromArray($array, $node_name) {
		$xml = '';

		if (is_array($array) || is_object($array)) {
			foreach ($array as $key => $value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}

				$xml .= '<' . strtolower($key) . '>' . self :: _generateXmlFromArray($value, $node_name) . '</' . strtolower($key) . '>';
			}
		} else {
			$xml = htmlspecialchars($array, ENT_QUOTES);
		}

		return $xml;
	}

}