<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * @author Frank Ammari, Ammari & Ammari GbR
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

		if (empty ($filenames)) {
			return array ();
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

		$return = array ();

		foreach ($images as $image) {
			if (in_array($image['filename'], $filenames)) {
				$return[array_search($image['filename'], $filenames)] = (object) $image;
			}
		}

		ksort($return);

		return $return;
	}

	public static function byFileExtension($idart, $fileextension) {

		if (empty ($fileextension)) {
			return array ();
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
		'	media.idart = :idart ' .
		'	and media.deleted is null ' .
		'	and media.extension = :fileextension ' .
		'	and media.mediaid in (' .
		'		select ' .
		'			max(media.mediaid) ' .
		'		from _media ' .
		'		where ' .
		'			idart = :idart ' .
		'		group by' .
		'			filename ' .
		'	)', array (
			':idart' => $idart,
			':idlang' => $idlang,
			':fileextension' => $fileextension
		));

		$return = array ();

		foreach ($images as $image) {
			$return[] = (object) $image;
		}

		return $return;
	}

	public static function byTag($idart, $tag) {

		if (empty ($idart) || empty ($tag)) {
			return array ();
		}

		$idlang = Aitsu_Registry :: get()->env->idlang;

		$images = Aitsu_Db :: fetchAll('' .
		'select ' .
		'	media.mediaid, ' .
		'	media.idart, ' .
		'	media.filename, ' .
		'	media.size, ' .
		'	media.extension, ' .
		'	media_tag.tag, ' .
		'	description.name, ' .
		'	description.subline, ' .
		'	description.description ' .
		'from _media media ' .
		'left join _media_description description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
		'left join _media_tags media_tags on media.mediaid = media_tags.mediaid ' .
		'left join _media_tag media_tag on media_tags.mediatagid = media_tag.mediatagid ' .
		'where ' .
		'	media.idart = :idart ' .
		'	and media_tag.tag = :tag ' .
		'	and media.deleted is null ' .
		'	and media.mediaid in (' .
		'		select ' .
		'			max(media.mediaid) ' .
		'		from _media ' .
		'		where ' .
		'			idart = :idart ' .
		'		group by' .
		'			filename ' .
		'	)', array (
			':idart' => $idart,
			':idlang' => $idlang,
			':tag' => $tag
		));

		$return = array ();

		foreach ($images as $image) {
			$return[] = (object) $image;
		}

		return $return;
	}
}