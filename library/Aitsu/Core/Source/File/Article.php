<?php


/**
 * Article centric media management file source.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Article.php 16281 2010-05-06 18:25:57Z akm $}
 */

class Aitsu_Core_Source_File_Article {

	protected function __construct() {
	}

	protected static function _getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function get($pattern = '*', $orderBy = 'filename asc') {

		$idart = Aitsu_Registry :: get()->env->idart;
		$pattern = str_replace('*', '%', $pattern);

		$files = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	* ' .
		'from _media as media ' .
		'left join _media_description as description on media.mediaid = description.mediaid ' .
		'where ' .
		'	media.idart = ? ' .
		'	and media.filename like ? ', array (
			$idart,
			$pattern
		));
		
		$return = array();
		
		if (!$files) {
			return $return;
		}
		
		foreach ($files as $file) {
			$return[] = (object) $file;
		}
		
		return $return;
	}
}