<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class MorasoCategoryController extends Aitsu_Adm_Plugin_Controller {

    const ID = '50a213b9-6568-4869-9fdd-56007f000001';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register($idcat) {

        $pos = self :: getPosition($idcat, 'moraso', 'category');

        return (object) array(
                    'name' => 'moraso',
                    'tabname' => Aitsu_Translate :: translate('Overview'),
                    'enabled' => $pos,
                    'position' => $pos,
                    'id' => self :: ID
        );
    }

    public function indexAction() {

        $user = Aitsu_Adm_User :: getInstance();
        $idcat = $this->getRequest()->getParam('idcat');
        $cat = Aitsu_Persistence_Category :: factory($idcat)->load();
        $idlang = Aitsu_Registry :: get()->session->currentLanguage;

        $this->view->usePublishing = Aitsu_Config::get('sys.usePublishing');
        $this->view->idcat = $idcat;
        $this->view->categoryname = $cat->name;
        $this->view->isInFavories = Aitsu_Persistence_CatFavorite :: factory($idcat)->load()->isInFavorites();
        $this->view->isClipboardEmpty = !isset(Aitsu_Registry :: get()->session->clipboard->articles) || count(Aitsu_Registry :: get()->session->clipboard->articles) == 0;

        $this->view->allowEdit = $user->isAllowed(array(
            'language' => $idlang,
            'area' => 'article',
            'action' => 'update',
            'resource' => array(
                'type' => 'cat',
                'id' => $idcat
                ))
        );

        $this->view->allowNew = $user->isAllowed(array(
            'language' => $idlang,
            'area' => 'article',
            'action' => 'insert',
            'resource' => array(
                'type' => 'cat',
                'id' => $idcat
                ))
        );

        $this->view->allowPublishing = $user->isAllowed(array(
            'language' => $idlang,
            'area' => 'article',
            'action' => 'publish',
            'resource' => array(
                'type' => 'cat',
                'id' => $idcat
                ))
        );
    }

    public function articlesAction() {

        $idcat = $this->getRequest()->getParam('idcat');
        $idlang = Aitsu_Registry :: get()->session->currentLanguage;

        if (isset($_POST['xaction']) && $_POST['xaction'] == 'update') {
            $data = json_decode($_POST['data']);
            $idcat = Aitsu_Db :: fetchOne('' .
                            'select ' .
                            '   idcat ' .
                            'from ' .
                            '   _cat_art ' .
                            'where ' .
                            '   idart = :idart', array(
                        ':idart' => $data->id
                    ));
            $arts = Aitsu_Db :: fetchAll('' .
                            'select ' .
                            '	artlang.idart, ' .
                            '	artlang.idartlang ' .
                            'from ' .
                            '   _art_lang as artlang ' .
                            'left join ' .
                            '   _cat_art as catart on artlang.idart = catart.idart ' .
                            'where ' .
                            '	catart.idcat = :idcat ' .
                            'and ' .
                            '   artlang.idlang = :idlang ' .
                            'order by ' .
                            '	artlang.artsort asc', array(
                        ':idcat' => $idcat,
                        ':idlang' => $idlang
                    ));
            $pos = 0;
            for ($i = 0; $i < count($arts); $i++) {
                $idart = $arts[$i]['idart'];
                $idartlang = $arts[$i]['idartlang'];

                if ($pos == $data->artsort) {
                    $pos++;
                }
                if ($idart == $data->id) {
                    $artsort = $data->artsort;
                } else {
                    $artsort = $pos++;
                }

                Aitsu_Db::put('_art_lang', 'idartlang', array(
                    'idartlang' => $idartlang,
                    'artsort' => $artsort
                ));

                Aitsu_Db :: query('' .
                        'update ' .
                        '   _pub_art_lang ' .
                        'set ' .
                        '   artsort =:artsort ' .
                        'where ' .
                        '   idartlang =:idartlang ' .
                        'and ' .
                        '   status =:status', array(
                    ':artsort' => $artsort,
                    ':idartlang' => $idartlang,
                    ':status' => 1
                ));
            }
        }

        $data = array();

        $arts = Aitsu_Persistence_View_Articles :: full($idcat, null);
        if ($arts) {
            foreach ($arts as $art) {
                $data[] = (object) array(
                            'id' => $art['idart'],
                            'title' => $art['title'],
                            'pagetitle' => $art['pagetitle'],
                            'urlname' => $art['urlname'],
                            'online' => $art['online'],
                            'published' => $art['published'],
                            'isstart' => $art['isstart'],
                            'artsort' => $art['artsort']
                );
            }
        }

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

}