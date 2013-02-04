<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Persistence_View_Media {

    public static function ofSpecifiedArticle($idart, $orderBy = 'media.filename asc') {

        return Aitsu_Db :: fetchAll('' .
                        'select ' .
                        '   media.mediaid, ' .
                        '   media.idart, ' .
                        '   media.filename, ' .
                        '   media.size, ' .
                        '   media.extension, ' .
                        '   description.name, ' .
                        '   description.subline, ' .
                        '   description.description ' .
                        'from ' .
                        '   _media as media ' .
                        'left join ' .
                        '   _media_description as description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '   (media.idart = :idart or media.idart is null)' .
                        'and ' .
                        '   media.deleted is null ' .
                        'and ' .
                        '   media.mediaid in (' .
                        '       select ' .
                        '           max(media.mediaid) ' .
                        '	from ' .
                        '           _media as media ' .
                        '	where ' .
                        '           (idart = :idart or idart is null)' .
                        '	group by' .
                        '           filename ' .
                        '   ) ' .
                        'order by ' .
                        '   ' . $orderBy, array(
                    ':idart' => $idart,
                    ':idlang' => Aitsu_Registry :: get()->env->idlang
                ));
    }

    public static function ofCurrentArticleWithoutDescription($orderBy = 'media.filename asc') {

        return Aitsu_Db :: fetchAll('' .
                        'select ' .
                        '	media.mediaid, ' .
                        '	media.idart, ' .
                        '	media.filename, ' .
                        '	media.size, ' .
                        '	media.extension, ' .
                        '	description.name, ' .
                        '	description.subline ' .
                        'from _media media ' .
                        'left join _media_description description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '	(media.idart = :idart or media.idart is null)' .
                        '	and media.deleted is null ' .
                        '	and media.mediaid in (' .
                        '		select ' .
                        '			max(media.mediaid) ' .
                        '		from _media media ' .
                        '		where ' .
                        '			(idart = :idart or idart is null)' .
                        '		group by' .
                        '			filename ' .
                        '	) ' .
                        'order by ' .
                        '	' . $orderBy, array(
                    ':idart' => Aitsu_Registry :: get()->env->idart,
                    ':idlang' => Aitsu_Registry :: get()->env->idlang
                ));
    }

}