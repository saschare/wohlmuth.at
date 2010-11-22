<?php


/**
 * @author Christian Kehres, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Config.php 19471 2010-10-21 13:30:53Z akm $}
 */

class Aitsu_Article_Config {

	private $_data = null;

	protected function __construct($idart, $idlang) {

		$defaultConfig = Aitsu_Db :: fetchOne('' .
		'select config from _configset where configsetid = 1');

		$defaultConfig = Aitsu_Util :: parseSimpleIni($defaultConfig);

		$config = Aitsu_Db :: fetchOne('' .
		'select configset.config ' .
		'from _configset as configset ' .
		'left join _art_lang as artlang on configset.configsetid = artlang.configsetid ' .
		'where ' .
		'	artlang.idart = :idart ' .
		'	and artlang.idlang = :idlang ' .
		'limit 0, 1', array (
			':idart' => $idart,
			':idlang' => $idlang
		));

		if (!$config) {
			$config = Aitsu_Db :: fetchOne('' .
			'select configset.config ' .
			'from _art_lang as artlang ' .
			'left join _cat_art as catart on artlang.idart = catart.idart ' .
			'left join _cat as child on catart.idcat = child.idcat ' .
			'left join _cat as parent on child.lft between parent.lft and parent.rgt ' .
			'left join _cat_lang as catlang on parent.idcat = catlang.idcat and artlang.idlang = catlang.idlang ' .
			'left join _configset as configset on catlang.configsetid = configset.configsetid ' .
			'where ' .
			'	artlang.idart = :idart ' .
			'	and artlang.idlang = :idlang ' .
			'	and configset.configsetid is not null ' .
			'order by parent.lft desc ' .
			'limit 0, 1', array (
				':idart' => $idart,
				':idlang' => $idlang
			));
		}

		if ($config) {
			$this->_data = Aitsu_Util :: parseSimpleIni($config, $defaultConfig);
		} else {
			$this->_data = $defaultConfig;
		}
	}

	public static function factory($idart = null) {

		static $instance = array ();

		$idart = $idart == null ? Aitsu_Registry :: get()->env->idart : $idart;
		$idlang = Aitsu_Registry :: get()->env->idlang;

		if (!isset ($instance[$idart . '_' . $idlang])) {
			$instance[$idart . '_' . $idlang] = new self($idart, $idlang);
		}

		return $instance[$idart . '_' . $idlang];
	}

	public function __get($key) {
		
		if (!isset($this->_data-> $key)) {
			return null;
		}

		return $this->_data-> $key;
	}
}