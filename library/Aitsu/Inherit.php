<?php


/**
 * Aitsu inherition class for articles.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Inherit.php 16693 2010-05-27 23:46:03Z akm $}
 */

class Aitsu_Inherit {

	protected $idcat;
	protected $idlang;
	protected $currentContent = null;
	protected $transformShortcodes = false;
	protected $field;
	protected $sourceIdartlang = null;

	protected function __construct($idcat, $idlang) {

		$this->idcat = $idcat != null ? $idcat : Aitsu_Registry :: get()->env->idcat;
		$this->idlang = $idlang != null ? $idlang : Aitsu_Registry :: get()->env->idlang;
	}

	public static function getInstance($idcat = null, $idlang = null) {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self($idcat, $idlang);
		}

		return $instance;
	}

	public function setCurrent($currentContent) {

		if ($this->_isEmpty($currentContent)) {
			$this->currentContent = null;
			return $this;
		}

		$this->currentContent = $currentContent;

		return $this;
	}

	public function inherit($type, $index, $transformShortcodes = false) {

		$this->field['type'] = $type;
		$this->field['index'] = $index;
		$this->transformShortcodes = $transformShortcodes;

		return $this;
	}

	public function getContent() {

		if ($this->currentContent != null) {
			return $this->currentContent;
		}

		$content = $this->_getInheritedContent();

		if ($this->transformShortcodes) {
			$content = preg_replace('/_\\[(.*?)\\:([^\\]]*)\\]/', "_[$1:$2:{$this->sourceIdartlang}]", $content);
		}

		return $content;
	}

	/**
	 * @deprecated 0.9.2 - 07.10.2010
	 */
	protected function _getInheritedContent() {
		
		return '';

		/*$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	catlang.startidartlang as idartlang, ' .
		'	content.value as value ' .
		'from _cat as child ' .
		'left join _cat as node on child.lft between node.lft and node.rgt ' .
		'left join _cat_lang as catlang on node.idcat = catlang.idcat ' .
		'left join _content as content on catlang.startidartlang = content.idartlang ' .
		'left join _type as contenttype on content.idtype = contenttype.idtype ' .
		'where ' .
		'	child.idcat = ? ' .
		'	and catlang.idlang = ? ' .
		'	and content.typeid = ? ' .
		'	and contenttype.type = ? ' .
		'order by ' .
		'	node.rgt asc ', array (
			$this->idcat,
			$this->idlang,
			$this->field['index'],
			$this->field['type']
		));

		if (!$results) {
			return '';
		}

		$content = '';

		foreach ($results as $result) {
			$content = urldecode($result['value']);
			if (!$this->_isEmpty($content)) {
				$this->sourceIdartlang = $result['idartlang'];
				return $content;
			}
		}

		return $content;*/
	}

	protected function _isEmpty($text) {

		return strlen(trim(html_entity_decode($text))) == 0;
	}
}