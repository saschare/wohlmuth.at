<?php


/**
 * Language chooser.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Language.php 16104 2010-04-23 09:13:12Z akm $}
 */

class Aitsu_Core_Navigation_Language {

	protected $idlang = null;
	protected $idart = null;
	protected $langs = array ();

	protected function __construct() {

		$this->idlang = Aitsu_Registry :: get()->env->idlang;
		$this->idart = Aitsu_Registry :: get()->env->idart;
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function registerLang($idlang, $name) {

		$this->langs[$idlang]['name'] = $name;
		$this->langs[$idlang]['url'] = null;
		$this->langs[$idlang]['active'] = false;
		$this->langs[$idlang]['current'] = $idlang == $this->idlang;
		
		return $this;
	}

	public function getLangs($omitFirst = true) {
		
		$whereInLangs = implode(', ', array_keys($this->langs));

		$langs = Aitsu_Db :: fetchAll('' .
		"select " .
		"	lang.idlang as idlang, " .
		"	lang.name as lang, " .
		"	if (startart.startidartlang = artlang.idartlang, concat(group_concat(catlang.urlname order by parent.lft asc separator '/'), '/'), concat(group_concat(catlang.urlname order by parent.lft asc separator '/'), '/', artlang.urlname, '.html')) as path, " .
		"	lang.active as active, " .
		"	artlang.online as online, " .
		"	catlang.visible as visible " .
		"from _art_lang as artlang " .
		"left join _cat_art as catart on artlang.idart = catart.idart " .
		"left join _cat as node on catart.idcat = node.idcat " .
		"left join _cat as parent on node.lft between parent.lft and parent.rgt and node.idclient = parent.idclient " .
		"left join _cat_lang as catlang on parent.idcat = catlang.idcat and artlang.idlang = catlang.idlang " .
		"left join _lang as lang on artlang.idlang = lang.idlang " .
		"left join _cat_lang as startart on artlang.idlang = startart.idlang and node.idcat = startart.idcat " .
		"where " .
		"	catart.idart = ? " .
		"	and catlang.idlang in ({$whereInLangs}) " .
		"group by " .
		"	node.idcat, " .
		"	artlang.urlname, " .
		"	catlang.idlang, " .
		"	lang.idlang, " .
		"	startart.startidartlang", array (
			$this->idart
		));
	
		foreach ($langs as $lang) {
			if ($omitFirst === true) {
				strtok($lang['path'], '/');
				$path = '/' . $lang['lang'] . '/' . strtok("\n");
			} else {
				$path = '/' . $lang['lang'] . '/' . $lang['path'];
			}
			$this->langs[$lang['idlang']]['url'] = $path;
			if ($lang['active'] == 1 && $lang['online'] == 1 && $lang['visible'] == 1) {
				$this->langs[$lang['idlang']]['active'] = true;
			} else {
				$this->langs[$lang['idlang']]['active'] = false;
			}
		}
		
		$return = array();
		
		foreach ($this->langs as $idlang => $lang) {
			$return[] = (object) $lang;
		}
		
		return $return;
	}
}