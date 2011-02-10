<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Transformation_StructuredTo {

	protected $_data = null;

	protected function __construct() {
	}

	public static function xml($data, $reverse = false) {

		if (empty ($data))
			return null;

		$instance = new self();
		$instance->_data = $data;

		if ($reverse) {
			$dom = new DOMDocument();
			$dom->loadXML($data);
			return $instance->_transformXmlToStruct($dom);
		}

		$dom = new DOMDocument('1.0', 'utf-8');
		$root = $dom->createElement('root');
		$dom->appendChild($root);
		$instance->_transformToXml($data, $root, $dom);

		return $dom->saveXML();
	}

	protected function _transformXmlToStruct($node) {

		$return = array ();

		if ($node->hasChildNodes()) {
			foreach ($node->childNodes as $childNode) {
				$children = $this->_transformXmlToStruct($childNode);
				if ($childNode->nodeName == 'root')
					return $children;
				if ($childNode->nodeName == 'node') {
					$return[] = (object) array (
						'id' => $childNode->attributes->getNamedItem('id')->nodeValue,
						'content' => $childNode->textContent,
						'children' => $children
					);
				}
			}
		}

		return $return;
	}

	protected function _transformToXml($data, & $node, & $dom) {

		if (0 == preg_match_all('/<\\!-{2}fragment\\:start\\s(([^\\-]*)\\-([^\\-]*))\\-{2}>(.*?)<\\!-{2}fragment\\:end\\s\\1\\-{2}>/', $data, $matches)) {
			return;
		}

		for ($i = 0; count($matches[0][$i]); $i++) {
			$module = $matches[2][$i];
			$index = $matches[3][$i];
			$content = $matches[4][$i];

			$id = $module;
			$id = empty ($index) ? $id : $id . '-' . $index;

			$childNode = $dom->createElement('node');
			$node->appendChild($childNode);

			$idAttr = $dom->createAttribute('id');
			$idAttr->appendChild($dom->createTextNode($id));
			$childNode->appendChild($idAttr);

			$childNode->appendChild($dom->createCDATASection(preg_replace('/<\\!\\-{2}fragment[^>]*>/', '', $content)));

			$this->_transformToXml($content, $childNode, $dom);
		}
	}
}