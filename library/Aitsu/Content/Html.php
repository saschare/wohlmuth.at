<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Content_Html {

	protected $idartlang = null;
	protected $token = null;
	protected $content = null;

	protected function __construct($token) {

		$this->idartlang = Aitsu_Registry :: get()->env->idartlang;
		$this->idcat = Aitsu_Registry :: get()->env->idcat;
		$this->idlang = Aitsu_Registry :: get()->env->idlang;
		$this->token = $token;
	}

	protected function factory($token) {

		static $instance = array ();

		if (!isset ($instance[$token])) {
			$instance[$token] = new self($token);
		}

		return $instance[$token];
	}

	public static function get($token, $words = null) {
		
		if ($words != null) {
			return Aitsu_Content :: get($token, Aitsu_Content :: HTML, null, null, $words);
		}

		return Aitsu_Content :: get($token, Aitsu_Content :: HTML);
	}

	public static function getInherited($token) {
		
		$content = Aitsu_Content :: get($token, null, null, null, 0);
		
		if (!empty($content)) {
			return $content;
		}

		$results = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	content.value as content, ' .
		'	content.idartlang as idartlang ' .
		'from _cat as child ' .
		'left join _cat as node on child.lft between node.lft and node.rgt ' .
		'left join _cat_lang as catlang on node.idcat = catlang.idcat ' .
		'left join _article_content as content on catlang.startidartlang = content.idartlang ' .
		'where ' .
		'	child.idcat = ? ' .
		'	and catlang.idlang = ? ' .
		'	and content.index = ? ' .
		'order by ' .
		'	node.rgt asc ', array (
			Aitsu_Registry :: get()->env->idcat,
			Aitsu_Registry :: get()->env->idlang,
			$token
		));

		if (!$results) {
			return '';
		}

		$content = '';

		foreach ($results as $result) {
			$content = stripslashes($result['content']);
			if (!empty($content)) {
				$content = preg_replace('/_\\[(.*\\:[^\\]]*)\\]/', "_[$1:{$result['idartlang']}]", $content);
				return $content;
			}
		}

		return $content;

	}
}