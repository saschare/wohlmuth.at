<?php

/**
 * @author Christian Kehres
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class PublicationhistoryArticleController extends Aitsu_Adm_Plugin_Controller {
    const ID = '4df8d3e8-f820-4dde-8a41-129a7f000101';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
            'name' => 'publicationhistory',
            'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Publication History'),
            'enabled' => self :: getPosition($idart, 'publicationhistory'),
            'position' => self :: getPosition($idart, 'publicationhistory'),
            'id' => self :: ID
        );
    }

    public function indexAction() {

        $this->view->idart = $this->getRequest()->getParam('idart');
    }

    public function storeAction() {

        $idart = $this->getRequest()->getParam('idart');

        $publications = Aitsu_Db::fetchAll("SELECT `pub`.*, concat(`user`.`lastname`, ', ', `user`.`firstname`) AS `user` FROM `_pub` as `pub` LEFT JOIN `_acl_user` AS `user` ON `pub`.`userid` = `user`.`userid` WHERE `pub`.`idartlang` =:idartlang", array(
                    ':idartlang' => Aitsu_Util::getIdArtLang($idart, Aitsu_Registry::get()->session->currentLanguage)
                ));

        $data = array();
        if ($publications) {
            foreach ($publications as $publication) {
                $data[] = (object) $publication;
            }
        }

        $this->_helper->json((object) array(
                    'data' => $data
        ));
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

        $this->idartlang = $this->getRequest()->getParam('idartlang');
        $pubid = $this->getRequest()->getParam('pubid');

        Aitsu_Db :: startTransaction();

        try {
            Aitsu_Db :: query('' .
                    'update _pub set status = 0 where idartlang = :idartlang', array(
                ':idartlang' => $this->idartlang
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
                        ':marker' => $this->$marker
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
                'art_' . $this->idart
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