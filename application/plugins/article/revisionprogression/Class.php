<?php

/**
 * @author Christian Kehres
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class RevisionprogressionArticleController extends Aitsu_Adm_Plugin_Controller {
    const ID = '4df8d3e8-f820-4dde-8a41-129a7f000101';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
            'name' => 'revisionprogression',
            'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Revision Progression'),
            'enabled' => self :: getPosition($idart, 'revisionprogression'),
            'position' => self :: getPosition($idart, 'revisionprogression'),
            'id' => self :: ID
        );
    }

    public function indexAction() {

        $this->view->idart = $this->getRequest()->getParam('idart');
    }

    public function storeAction() {

        $idart = $this->getRequest()->getParam('idart');

        $publications = Aitsu_Db::fetchAll("
            SELECT
                `pub`.`pubid`,
                `pub`.`idartlang`,
                `pub`.`pubtime`,
                `pub`.`status` AS `isPublished`,
                concat(`user`.`lastname`, ', ', `user`.`firstname`) AS `user`
            FROM
                `_pub` as `pub`
            RIGHT JOIN
                `_acl_user` AS `user` ON `pub`.`userid` = `user`.`userid`
            WHERE
                `pub`.`idartlang` =:idartlang
            ORDER BY
                `pub`.`pubid` DESC", array(
                    ':idartlang' => Aitsu_Util::getIdArtLang($idart, Aitsu_Registry::get()->session->currentLanguage)
                ));

        $data = array();
        if ($publications) {
            foreach ($publications as $publication) {
                $publication['isEdit'] = self::isEdit($publication['idartlang'], $publication['pubid']);
                $data[] = (object) $publication;
            }
        }

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public static function isEdit($idartlang, $pubid) {

        $isPublished = Aitsu_Db :: fetchOne('' .
                        'select count(*) ' .
                        'from _art_lang as artlang, _pub as pub ' .
                        'where artlang.idartlang = pub.idartlang ' .
                        'and artlang.lastmodified = pub.pubtime ' .
                        'and pub.pubid = :pubid ' .
                        'and artlang.idartlang = :idartlang', array(
                    ':pubid' => $pubid,
                    ':idartlang' => $idartlang
                ));

        if ($isPublished > 0) {
            return true;
        }

        return false;
    }

    public function activateAction() {

        try {
            $idart = $this->getRequest()->getParam('idart');
            $pubid = $this->getRequest()->getParam('pubid');

            Aitsu_Persistence_Article::factory($idart)->rebuild($pubid);
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false
            ));
        }

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function publishAction() {

        $idartlang = $this->getRequest()->getParam('idartlang');
        $pubid = $this->getRequest()->getParam('pubid');

        $idart = Aitsu_Util::getIdArt($idartlang);

        Aitsu_Db :: startTransaction();

        try {

            $article = Aitsu_Persistence_Article::factory($idart)->load();

            Aitsu_Db :: query('' .
                    'update _pub set status = 0 where idartlang = :idartlang', array(
                ':idartlang' => $idartlang
            ));

            Aitsu_Db :: query('' .
                    'update _pub set status = 1 where pubid = :pubid', array(
                ':pubid' => $pubid
            ));

            $publishMap = new Zend_Config_Ini(APPLICATION_PATH . '/configs/publishmap.ini');

            foreach ($publishMap as $type => $tables) {
                foreach ($tables->toArray() as $table) {
                    $marker = $table['marker'];

                    Aitsu_Db :: query('' .
                            'update ' . $table['target'] . ' set ' .
                            'status = 0 ' .
                            'where ' . $marker . ' = :marker', array(
                        ':marker' => $article->$marker
                    ));

                    Aitsu_Db :: query('' .
                            'update ' . $table['target'] . ' set ' .
                            'status = 1 ' .
                            'where pubid = :pubid', array(
                        ':pubid' => $pubid
                    ));
                }
            }

            Aitsu_Cache :: getInstance()->clean(array(
                'art_' . $article->idart
            ));

            Aitsu_Db :: commit();
        } catch (Exception $e) {
            Aitsu_Db :: rollback();
            $this->_helper->json((object) array(
                        'success' => false
            ));
        }

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

}