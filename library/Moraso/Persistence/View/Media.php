<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Persistence_View_Media extends Aitsu_Persistence_View_Media {

    public static function ofSpecifiedArticle($idart = null, $description = true, $orderBy = 'media.filename asc') {

        if (empty($idart)) {
            $idart = Aitsu_Registry :: get()->env->idart;
        }

        $selects = array(
            'media.mediaid',
            'media.idart',
            'media.filename',
            'media.size',
            'media.extension',
            'description.name',
            'description.subline'
        );

        if ($description) {
            $selects[] = 'description.description';
        }

        $images = Aitsu_Db :: fetchAll('' .
                        'select ' .
                        '   ' . implode(',', $selects) . ' ' .
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

        $return = array();
        foreach ($images as $image) {
                $return[] = (object) $image;
        }

        return $return;
    }

}