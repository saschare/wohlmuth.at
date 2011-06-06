<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Persistence_View_Media {

	public static function ofCurrentArticle() {

		return Aitsu_Db :: fetchAll('' .
		'select ' .
		'	media.mediaid, ' .
		'	media.idart, ' .
		'	media.filename, ' .
		'	media.size, ' .
		'	media.extension, ' .
		'	description.name, ' .
		'	description.subline, ' .
		'	description.description ' .
		'from _media media ' .
		'left join _media_description description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
		'where ' .
		'	(media.idart = :idart or media.idart is null)' .
		'	and media.deleted is null ' .
		'	and media.mediaid in (' .
		'		select ' .
		'			max(media.mediaid) ' .
		'		from _media ' .
		'		where ' .
		'			(idart = :idart or idart is null)' .
		'		group by' .
		'			filename ' .
		'	)', array (
			':idart' => Aitsu_Registry :: get()->env->idart,
			':idlang' => Aitsu_Registry :: get()->env->idlang
		));
	}
        
	public static function byFileName($idart, $filenames) {
		
		if (empty($filenames)) {
			return array();
		}

		$idlang = Aitsu_Registry :: get()->env->idlang;

		$images = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	media.mediaid, ' .
		'	media.idart, ' .
		'	media.filename, ' .
		'	media.size, ' .
		'	media.extension, ' .
		'	description.name, ' .
		'	description.subline, ' .
		'	description.description ' .
		'from _media media ' .
		'left join _media_description description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
		'where ' .
		'	(media.idart = :idart or media.idart is null)' .
		'	and media.deleted is null ' .
		'	and media.mediaid in (' .
		'		select ' .
		'			max(media.mediaid) ' .
		'		from _media ' .
		'		where ' .
		'			(idart = :idart or idart is null)' .
		'		group by' .
		'			filename ' .
		'	)', array (
			':idart' => $idart,
			':idlang' => $idlang
		));
		
		$return = array();
		
		foreach ($images as $image) {
			if (in_array($image['filename'], $filenames)) {
				$return[array_search($image['filename'], $filenames)] = (object) $image;
			}
		}
		
		ksort($return);
		
		return $return;
	}
}