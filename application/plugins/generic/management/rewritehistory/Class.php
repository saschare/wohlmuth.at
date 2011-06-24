<?php

/**
 * @author Christian Kehres
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class RewritehistoryPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {
        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Aitsu_Db::fetchAll("
            SELECT
                `history`.`id`,
                `history`.`url`,
                `history`.`idartlang`
            FROM
                `_aitsu_rewrite_history` AS `history`
            WHERE
                `history`.`manualentry` = :manualentry
            AND
                `history`.`idclient` = :idclient
        ", array(
                    ':manualentry' => 1,
                    ':idclient' => Aitsu_Registry::get()->session->currentClient
                ));
        
        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('entry', APPLICATION_PATH . '/plugins/generic/management/rewritehistory/forms/edit.ini');
        $form->title = Aitsu_Translate :: translate('Edit rewrite Rule');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        $data = Aitsu_Db::fetchRow("
            SELECT
                `history`.`id`,
                `history`.`url`,
                `history`.`idartlang`
            FROM
                `_aitsu_rewrite_history` AS `history`
            WHERE
                `history`.`id` = :id
        ", array(
                    ':id' => $id
                ));

        $form->setValues($data);

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {

                $data = $form->getValues();
                $data['idclient'] = Aitsu_Registry::get()->session->currentClient;
                $data['manualentry'] = 1;

                Aitsu_Db :: put('_rewrite_history', 'id', $data);

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

        Aitsu_Db::query("DELETE FROM `_rewrite_history` WHERE `id` =:id", array(':id' => $id));

        $this->_helper->json((object) array(
                    'success' => true,
        ));
    }

}