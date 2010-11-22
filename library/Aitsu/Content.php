<?php


/**
 * aitsu Content.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Content.php 16732 2010-05-31 13:28:44Z akm $}
 */

/*
CREATE TABLE `con_article_content` (
`idartlang` INT UNSIGNED NOT NULL ,
`index` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`value` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (  `idartlang` ,  `index` )
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
*/

class Aitsu_Content {

	const PLAINTEXT = 1;
	const HTML = 2;
	const WIKICODE = 3;

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

	protected function getInstance($index, $type, $idart, $idlang) {

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

	public static function get($index, $type = null, $idart = null, $idlang = null, $words = 50) {

		$i = self :: getInstance($index, $type, $idart, $idlang);

		Aitsu_Content_Edit :: registerContent((object) array (
			'index' => $i->index,
			'type' => $i->type,
			'idart' => $i->idart,
			'idlang' => $i->idlang
		));
		
		if (isset(Aitsu_Registry :: get()->env->substituteEmptyAreas) && Aitsu_Registry :: get()->env->substituteEmptyAreas == true) {
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

		Aitsu_Db :: query('' .
		'replace into _article_content ' .
		'(idartlang, `index`, `value`, modified) ' .
		'values ' .
		'(?, ?, ?, now()) ', array (
			$idartlang,
			$index,
			$content
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