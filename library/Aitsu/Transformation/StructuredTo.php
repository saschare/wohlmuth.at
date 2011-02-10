<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Aitsu_Transformation_StructuredTo {

	protected $_data = null;

	protected function __construct() {
	}

	public static function xml($data) {

		if (empty ($data))
			return null;

		$instance = new self();
		$instance->_data = $data;

		$dom = new DOMDocument('1.0', 'utf-8');
		$root = $dom->createElement('root');
		$dom->appendChild($root);
		$instance->_transformToXml($data, $root, $dom);

		return $dom->saveXML();
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
			
			$childnode = $dom->createElement('node');
			$node->appendChild($childnode);
			
			$idAttr = $dom->createAttribute('id');
			$idAttr->appendChild($dom->createTextNode($id));
			$childnode->appendChild($idAttr);
			
			$contentNode = $dom->createElement('content');
			$contentNode->appendChild($dom->createCDATASection(preg_replace('/<\\!\\-{2}fragment[^>]*>/', '', $content)));
			$node->appendChild($contentNode);
			
			$this->_transformToXml($content, $childnode, $dom);

			/*$children = $this->_transformToXml($content);

			$return[] = (object) array (
				'id' => $id,
				'content' => $content,
				'children' => empty ($children) ? null : $children
			);*/
		}
	}
}