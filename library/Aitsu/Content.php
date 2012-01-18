<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
class Aitsu_Content {

	const PLAINTEXT = 1;
	const HTML = 2;
	const WIKICODE = 3;
	const TEXTILE = 4;

	protected $index;
	protected $type;
	protected $value;
	protected $idartlang;
	protected $idart;
	protected $idlang;

	protected function __construct($index, $type, $idart, $idlang) {

		$this->index = $index;
		$this->type = $type;
		$this->idart = $idart;
		$this->idlang = $idlang;

		$this->_restore();
	}

	protected static function getInstance($index, $type, $idart, $idlang) {

		static $instance = array ();

		$type = $type != null ? $type : self :: HTML;
		$idart = $idart != null ? $idart : Aitsu_Registry :: get()->env->idart;
		$idlang = $idlang != null ? $idlang : Aitsu_Registry :: get()->env->idlang;

		$token = $index . '-' . $idart . '-' . $idlang;

		if (!isset ($instance[$token])) {
			$instance[$token] = new self($index, $type, $idart, $idlang);
		}

		return $instance[$token];
	}

	public static function get($index, $type = null, $idart = null, $idlang = null, $words = 50, $forceReload = false, $configTab = true) {

		$i = self :: getInstance($index, $type, $idart, $idlang);

		if ($forceReload) {
			$i->_restore();
		}

		if ($configTab) {
			Aitsu_Content_Edit :: registerContent((object) array (
				'index' => $i->index,
				'type' => $i->type,
				'idart' => $i->idart,
				'idlang' => $i->idlang
			));
		}

		if (isset (Aitsu_Registry :: get()->env->substituteEmptyAreas) && Aitsu_Registry :: get()->env->substituteEmptyAreas == true) {
			$subst = true;
		} else {
			$subst = false;
		}

		if (strlen(trim($i->value)) == 0 && (Aitsu_Registry :: isEdit() || $subst == true) && $words != null) {
			if (is_numeric($words)) {
				return Aitsu_LoremIpsum :: get($words);
			} else {
				return $words;
			}
		}

		return $i->value;
	}

	public static function set($index, $idartlang, $content) {

		Aitsu_Event :: raise('article.content.set.start', array (
			'idartlang' => $idartlang,
			'action' => 'save'
		));

		Aitsu_Db :: query('' .
		'replace into _article_content ' .
		'(idartlang, `index`, `value`, modified) ' .
		'values ' .
		'(?, ?, ?, now()) ', array (
			$idartlang,
			$index,
			str_replace("\\", "\\\\", $content)
		));

		Aitsu_Event :: raise('article.content.set.end', array (
			'idartlang' => $idartlang,
			'action' => 'save'
		));

		Aitsu_Persistence_Article :: touch($idartlang);
	}

	protected function _restore() {

		$result = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	content.idartlang, ' .
		'	content.value ' .
		'from _article_content as content ' .
		'left join _art_lang as artlang on content.idartlang = artlang.idartlang ' .
		'where ' .
		'	artlang.idart = ? ' .
		'	and artlang.idlang = ? ' .
		'	and content.index = ? ' .
		'limit 0, 1 ', array (
			$this->idart,
			$this->idlang,
			$this->index
		));

		if (!$result) {
			return;
		}

		$this->value = stripslashes($result['value']);
		$this->idartlang = $result['idartlang'];
	}

}