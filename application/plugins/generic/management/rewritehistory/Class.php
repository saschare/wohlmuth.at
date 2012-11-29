<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class RewritehistoryPluginController extends Aitsu_Adm_Plugin_Controller {

    public function init() {
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {
        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   history.id, ' .
                        '   history.url, ' .
                        '   artlang.idart, ' .
                        '   if (catlang.url <> "", concat("/", catlang.url, "/", artlang.urlname, ".html"), concat("/", artlang.urlname, ".html")) as target ' .
                        'from ' .
                        '   _aitsu_rewrite_history as history ' .
                        'left join ' .
                        '   _art_lang as artlang on artlang.idartlang = history.idartlang ' .
                        'left join ' .
                        '   _cat_art as catart on catart.idart = artlang.idart ' .
                        'left join ' .
                        '   _cat_lang as catlang on (catlang.idcat = catart.idcat and catlang.idlang = artlang.idlang) ' .
                        'order by ' .
                        '   history.id desc');

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function editAction() {

        $id = $this->getRequest()->getParam('id');

        $this->_helper->layout->disableLayout();

        $form = Aitsu_Forms::factory('entry', APPLICATION_PATH . '/plugins/generic/management/rewritehistory/forms/edit.ini');
        $form->title = Aitsu_Translate::translate('Edit rewrite Rule');
        $form->url = $this->view->url(array('paction' => 'edit'), 'plugin');

        $data = Moraso_Db::fetchRow('' .
                        'select ' .
                        '   history.id, ' .
                        '   history.url, ' .
                        '   history.idartlang ' .
                        'from ' .
                        '   _aitsu_rewrite_history as history ' .
                        'where ' .
                        '   history.id =:id', array(
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
                $data['manualentry'] = 1;

                $idlang = Aitsu_Registry::get()->session->currentLanguage;

                if (strpos($data['idartlang'], 'idart') !== false) {
                    $data['idartlang'] = Aitsu_Util::getIdArtLang(substr($data['idartlang'], 6), $idlang);
                } elseif (strpos($data['idartlang'], 'idcat') !== false) {
                    $data['idartlang'] = Moraso_Db::fetchOne('' .
                                    'select ' .
                                    '   startidartlang ' .
                                    'from ' .
                                    '   _cat_lang ' .
                                    'where ' .
                                    '   idcat =:idcat ' .
                                    'and ' .
                                    '   idlang =:idlang', array(
                                ':idcat' => substr($data['idartlang'], 6),
                                ':idlang' => $idlang
                            ));
                }

                if (empty($data['id'])) {
                    unset($data['id']);
                }

                Moraso_Db::put('_aitsu_rewrite_history', 'id', $data);

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

        $this->_helper->layout->disableLayout();

        Moraso_Db::query('' .
                'delete from ' .
                '   _aitsu_rewrite_history ' .
                'where ' .
                '   id =:id', array(
            ':id' => $this->getRequest()->getParam('id')
        ));

        $this->_helper->json((object) array(
                    'success' => true,
        ));
    }

}