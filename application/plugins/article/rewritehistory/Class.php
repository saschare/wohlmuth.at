<?php

/**
 * @author Christian Kehres
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class RewritehistoryArticleController extends Aitsu_Adm_Plugin_Controller {
    const ID = '4cbd68e4-6b4c-487c-9fd7-13237f000201';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
            'name' => 'rewritehistory',
            'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Rewrite History'),
            'enabled' => self :: getPosition($idart, 'rewritehistory'),
            'position' => self :: getPosition($idart, 'rewritehistory'),
            'id' => self :: ID
        );
    }

    public function indexAction() {

        $this->view->idart = $this->getRequest()->getParam('idart');
    }

    public function storeAction() {

        $idart = $this->getRequest()->getParam('idart');

        $data = Aitsu_Db::fetchAll("
            SELECT
                `history`.`id`,
                `history`.`url`,
                CONCAT(`catlang`.`url`, '/', `artlang`.`urlname`, '.html') AS `target`
            FROM
                `_aitsu_rewrite_history` AS `history`
            LEFT JOIN
                `_art_lang` AS `artlang` ON `artlang`.`idartlang` = `history`.`idartlang`
            LEFT JOIN
                `_cat_art` AS `catart` ON `catart`.`idart` = `artlang`.`idart`
            LEFT JOIN
                `_cat_lang` AS `catlang` ON (`catlang`.`idcat` = `catart`.`idcat` AND `catlang`.`idlang` = `artlang`.`idlang`)
            WHERE
                `artlang`.`idart` = :idart
            ", array(
                    ':idart' => $idart
                ));

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');
        $idart = $this->getRequest()->getParam('idart');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('entry', APPLICATION_PATH . '/plugins/article/rewritehistory/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit rewrite Rule');
        $form->url = $this->view->url(array('plugin' => 'rewritehistory', 'paction' => 'edit'), 'aplugin');

        $data = Aitsu_Db::fetchRow("
            SELECT
                `history`.`id`,
                `history`.`url`
            FROM
                `_aitsu_rewrite_history` AS `history`
            WHERE
                `history`.`id` = :id
        ", array(
                    ':id' => $id
                ));

        $data['idart'] = $idart;
        $form->setValues($data);

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();
                $data['manualentry'] = 1;

                $idlang = Aitsu_Registry::get()->session->currentLanguage;
                
                $data['idartlang'] = Aitsu_Util::getIdArtLang($idart, $idlang);

                $primarykey = null;

                if (empty($data['id'])) {
                    unset($data['id']);
                }

                Aitsu_Db :: put('_aitsu_rewrite_history', 'id', $data);


                $this->_helper->json((object) array(
                            'success' => true,
                            'data' => (object) $data
                ));
            } else {
                $this->_helper->json((object) array(
                            'success' => false,
                            'errors' => $form->getErrors()
                ));
            }
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false,
                        'exception' => true,
                        'message' => $e->getMessage()
            ));
        }
    }

    public function deleteAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        Aitsu_Db::query("DELETE FROM `_aitsu_rewrite_history` WHERE `id` =:id", array(':id' => $id));

        $this->_helper->json((object) array(
                    'success' => true,
        ));
    }

}