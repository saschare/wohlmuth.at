<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Article {

    protected $idart;
    protected $idartlang;
    protected $idclient;

    protected function __construct($idart) {

        $this->idart = $idart;
        $this->idartlang = Aitsu_Db::fetchOneC('eternal', '' .
                        'select ' .
                        '   idartlang ' .
                        'from ' .
                        '   _art_lang ' .
                        'where ' .
                        '   idart =:idart ' .
                        'and ' .
                        '   idlang =:idlang', array(
                    ':idart' => $idart,
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
                        )
        );
        $this->idclient = Aitsu_Db::fetchOneC('eternal', '' .
                        'select ' .
                        '   idclient ' .
                        'from ' .
                        '   _art ' .
                        'where ' .
                        '   idart =:idart', array(
                    ':idart' => $idart
                        )
        );
    }

    public static function getInstance($idart = null) {

        static $instance = null;

        if (!isset($instance)) {
            $instance = new self($idart);
        }

        return $instance;
    }

    public function duplicate() {

        // _art
        $newIdArt = Aitsu_Db::put('_art', 'idart', array(
                    'idclient' => $this->idclient
                ));

        // _art_lang
        $oldArtLangData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _art_lang ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );
        unset($oldArtLangData['idartlang']);
        $oldArtLangData['idart'] = $newIdArt;

        $newIdArtLang = Aitsu_Db::put('_art_lang', 'idartlang', $oldArtLangData);

        // _art_content
        $oldArticleContentData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _article_content ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );

        if (!empty($oldArticleContentData)) {
            $oldArticleContentData['idartlang'] = $newIdArtLang;

            Aitsu_Db::put('_article_content', null, $oldArtLangData);
        }

        // _art_geolocation
        $oldArtGeolocationData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _art_geolocation ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );

        if (!empty($oldArtGeolocationData)) {
            $oldArtGeolocationData['idartlang'] = $newIdArtLang;

            Aitsu_Db::put('_art_geolocation', null, $oldArtGeolocationData);
        }

        // _art_meta
        $oldArtMetaData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _art_meta ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );

        if (!empty($oldArtMetaData)) {
            $oldArtMetaData['idartlang'] = $newIdArtLang;

            Aitsu_Db::put('_art_meta', null, $oldArtMetaData);
        }

        // _art_timecontrol
        $oldArtTimecontrolData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _art_timecontrol ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );

        if (!empty($oldArtTimecontrolData)) {
            $oldArtTimecontrolData['idartlang'] = $newIdArtLang;

            Aitsu_Db::put('_art_timecontrol', null, $oldArtTimecontrolData);
        }

        // _cat_art
        $oldCatArtData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _cat_art ' .
                        'where ' .
                        '   idart =:idart', array(
                    ':idart' => $this->idart
                        )
        );
        $oldCatArtData['idart'] = $newIdArt;

        Aitsu_Db::put('_cat_art', null, $oldCatArtData);

        // _channel_art_lang
        $oldChannelArtLangData = Aitsu_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _channel_art_lang ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );
        if (!empty($oldChannelArtLangData)) {
            $oldChannelArtLangData['idartlang'] = $newIdArtLang;

            Aitsu_Db::put('_channel_art_lang', null, $oldChannelArtLangData);
        }

        // _media
        $oldMediaData = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _media ' .
                        'where ' .
                        '   idart =:idart', array(
                    ':idart' => $this->idart
                        )
        );

        if (!empty($oldMediaData)) {
            Moraso_Util_Dir::copy(APPLICATION_PATH . '/data/media/' . $this->idart . '/', APPLICATION_PATH . '/data/media/' . $newIdArt . '/');
            
            foreach ($oldMediaData as $row) {
                $oldMediaId = $row['mediaid'];

                unset($row['mediaid']);
                $row['idart'] = $newIdArt;

                $newMediaId = Aitsu_Db::put('_media', 'mediaid', $row);

                // _media_description
                $oldMediaDescriptionData = Aitsu_Db::fetchRow('' .
                                'select ' .
                                '   * ' .
                                'from ' .
                                '   _media_description ' .
                                'where ' .
                                '   mediaid =:mediaid ' .
                                'and ' .
                                '   idlang =:idlang', array(
                            ':mediaid' => $oldMediaId,
                            ':idlang' => Aitsu_Registry::get()->session->currentLanguage
                                )
                );
                if (!empty($oldMediaDescriptionData)) {
                    $oldMediaDescriptionData['mediaid'] = $newMediaId;

                    Aitsu_Db::put('_media_description', null, $oldMediaDescriptionData);
                }

                // _media_tags
                $oldMediaTagsData = Aitsu_Db::fetchAll('' .
                                'select ' .
                                '   * ' .
                                'from ' .
                                '   _media_tags ' .
                                'where ' .
                                '   mediaid =:mediaid', array(
                            ':mediaid' => $oldMediaId
                                )
                );
                if (!empty($oldMediaTagsData)) {
                    foreach ($oldMediaTagsData as $row) {
                        $row['mediaid'] = $newMediaId;

                        Aitsu_Db::put('_media_tags', null, $row);
                    }
                }
            }
        }

        // _tag_art
        $oldTagArtData = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _tag_art ' .
                        'where ' .
                        '   idart =:idart', array(
                    ':idart' => $this->idart
                        )
        );
        if (!empty($oldTagArtData)) {
            foreach ($oldTagArtData as $row) {
                $row['idart'] = $newIdArt;

                Aitsu_Db::put('_tag_art', null, $row);
            }
        }

        // _aitsu_article_property
        $oldAitsuArticlePropertyData = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _aitsu_article_property ' .
                        'where ' .
                        '   idartlang =:idartlang', array(
                    ':idartlang' => $this->idartlang
                        )
        );
        if (!empty($oldAitsuArticlePropertyData)) {
            foreach ($oldAitsuArticlePropertyData as $row) {
                $row['idartlang'] = $newIdArtLang;

                Aitsu_Db::put('_aitsu_article_property', null, $row);
            }
        }

        return $newIdArt;
    }

}