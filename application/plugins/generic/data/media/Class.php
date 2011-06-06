<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @author Christian Kehres, webtischlerei
 * 
 * @copyright Copyright &copy; 2010, w3concepts AG
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class MediaPluginController extends Aitsu_Adm_Plugin_Controller {
    const ID = '4dec88a2-90c4-4ccf-b9bf-0ae07f000101';

    protected $_idartlang;

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        $this->view->idart = 0;
    }

    public function storeAction() {
        
        $idart = null;
        $idlang = Aitsu_Registry::get()->session->currentLanguage;

        $files = Aitsu_File::getFiles($idart, $idlang, '*', 'filename', true, true);

        $data = array();
        if ($files) {
            foreach ($files as $file) {
                $data[] = (object) $file;
            }
        }

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function tagstoreAction() {

        $mediaid = $this->getRequest()->getParam('mediaid');
        $tags = Aitsu_Persistence_File :: factory($mediaid)->getTags();

        $data = array();
        if ($tags) {
            foreach ($tags as $tag) {
                $data[] = (object) $tag;
            }
        }

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

    public function addtagAction() {

        $mediaid = $this->getRequest()->getParam('mediaid');
        $token = $this->getRequest()->getParam('token');
        $value = $this->getRequest()->getParam('value');

        if (!empty($token)) {
            Aitsu_Persistence_File :: factory($mediaid)->addTag($token, $value);
        }

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function atagstoreAction() {

        $filter = array(
            (object) array(
                'clause' => 'tag like',
                'value' => '%' . $this->getRequest()->getParam('query') . '%'
            )
        );

        $this->_helper->json((object) array(
                    'data' => Aitsu_Persistence_MediaTag :: getStore(100, 0, $filter)
        ));
    }

    public function removetagAction() {

        $mediaid = $this->getRequest()->getParam('mediaid');
        $mediatagid = $this->getRequest()->getParam('mediatagid');

        Aitsu_Persistence_File :: factory($mediaid)->removeTag($mediatagid);

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function uploadAction() {

        Aitsu_File::upload(null, $_FILES['file']['name'], $_FILES['file']['tmp_name']);

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function deleteAction() {

        $mediaid = $this->getRequest()->getParam('mediaid');

        Aitsu_File::delete($mediaid);

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

    public function saveAction() {
        
        $mediaid = $this->getRequest()->getParam('mediaid');
        $idlang = Aitsu_Registry::get()->session->currentLanguage;

        try {
            $file = Aitsu_File::factory($mediaid, $idlang);
            $file->filename = $this->getRequest()->getParam('filename');
            $file->medianame = $this->getRequest()->getParam('name');
            $file->subline = $this->getRequest()->getParam('subline');
            $file->description = $this->getRequest()->getParam('description');
            $file->xtl = $this->getRequest()->getParam('xtl');
            $file->ytl = $this->getRequest()->getParam('ytl');
            $file->xbr = $this->getRequest()->getParam('xbr');
            $file->ybr = $this->getRequest()->getParam('ybr');
            $file->save();

            $this->_helper->json(array(
                'success' => true
            ));
        } catch (Exception $e) {
            $this->_helper->json(array(
                'success' => false,
                'status' => 'exception',
                'message' => $e->getMessage()
            ));
        }
    }

}