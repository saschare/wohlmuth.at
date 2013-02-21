<?php

/**
 * @author Andreas Kummer <a.kummer@wdrei.ch>
 * @copyright (c) 2013, Andreas Kummer
 */
class TranslationController extends Zend_Controller_Action {

    public function init() {

        if (!Aitsu_Adm_User :: getInstance()->isAllowed(array(
                    'area' => 'translation'
                ))) {
            throw new Exception('Access denied');
        }

        $this->_helper->layout->disableLayout();

        $this->_filter = Aitsu_Util_ExtJs :: encodeFilters($this->getRequest()->getParam('filter'));
    }

    public function indexAction() {
        
    }

    public function storeAction() {

        $refresh = $this->getRequest()->getParam('refresh');

        if ($refresh == 1) {
            Aitsu_Translate :: populate(Aitsu_Registry :: get()->session->currentLanguage);
        }

        $this->_helper->json((object) array(
                    'data' => Aitsu_Persistence_Translate :: getStore(100, 0, $this->_filter)
        ));
    }

    public function editAction() {

        $this->_helper->layout->disableLayout();

        $id = $this->getRequest()->getParam('translationid');

        $form = Aitsu_Forms :: factory('edittranslation', APPLICATION_PATH . '/adm/forms/translation/translation.ini');
        $form->title = Aitsu_Translate :: translate('Edit translation');
        $form->url = $this->view->url();

        $data = Aitsu_Persistence_Translate :: factory($id)->load()->toArray();
        $form->setValues($data);

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {
                $values = $form->getValues();

                /*
                 * Update config set.
                 */
                Aitsu_Persistence_Translate :: factory($id)->load()->setValues($values)->save();

                $this->_helper->json((object) array(
                            'success' => true
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

        Aitsu_Persistence_Translate :: factory($this->getRequest()->getParam('translationid'))->remove();

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function exportAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $filename = 'translation.';
        $filename .= Aitsu_Registry :: get()->session->currentClient . '.';
        $filename .= Aitsu_Registry :: get()->session->currentLanguage . '.';
        $filename .= date('Y-m-d-H-i-s') . '.xml';

        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo Aitsu_Filter_ToXml :: get(array(
            'info' => array(
                'type' => 'translation',
                'date' => date('Y-m-d H:i:s'),
                'client' => Aitsu_Registry :: get()->session->currentClient,
                'language' => array(
                    'id' => Aitsu_Registry :: get()->session->currentLanguage,
                    'locale' => Aitsu_Persistence_Language :: factory(Aitsu_Registry :: get()->session->currentLanguage)->load()->locale
                ),
                'system' => array(
                    'HTTP_HOST' => $_SERVER['HTTP_HOST'],
                    'SERVER_NAME' => $_SERVER['SERVER_NAME'],
                    'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
                    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
                )
            ),
            'data' => Aitsu_Persistence_Translate :: getByLanguage(Aitsu_Registry :: get()->session->currentLanguage)
        ))->saveXML();
    }

}