<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class LuceneArticleController extends Aitsu_Adm_Plugin_Controller {

    const ID = '5156f61e-d780-4b15-aa48-2a37c0a8b230';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        return (object) array(
                    'name' => 'lucene',
                    'tabname' => Aitsu_Registry::get()->Zend_Translate->translate('Lucene'),
                    'enabled' => self::getPosition($idart, 'lucene'),
                    'position' => self::getPosition($idart, 'lucene'),
                    'id' => self::ID
        );
    }

    public function indexAction() {

        $idart = $this->getRequest()->getParam('id');
        $idlang = Moraso_Util::getIdlang();

        $form = Aitsu_Forms::factory('lucene', APPLICATION_PATH . '/plugins/article/lucene/forms/lucene.ini');
        $form->title = Aitsu_Translate::translate('Lucene');
        $form->url = $this->view->url(array('plugin' => 'lucene', 'paction' => 'index'), 'aplugin');

        $form->setValue('idart', $idart);

        $data = Moraso_Db::fetchRow('' .
                        'select ' .
                        '   uid, ' .
                        '   lastindexed ' .
                        'from ' .
                        '   _lucene_index ' .
                        'where ' .
                        '   idart =:idart ' .
                        'and ' .
                        '   idlang =:idlang', array(
                    ':idart' => $idart,
                    ':idlang' => $idlang
        ));

        $form->setValues($data);

        $client_config = new Zend_Config_Ini('application/configs/clients/default.ini', Moraso_Util::getEnv());

        $indexPath = APPLICATION_PATH . '/data/lucene/' . $client_config->search->lucene->index . '/';

        $index = Zend_Search_Lucene::open($indexPath);

        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('UTF-8');

        $hits = $index->find('uid:' . $data['uid'] . ' AND lang:' . $idlang);

        if (isset($hits[0]) && !empty($hits[0])) {
            $luceneDocument = $hits[0]->getDocument();
                        
            $form->setValue('uid', $luceneDocument->uid);
            $form->setValue('title', $luceneDocument->title);
            $form->setValue('pagetitle', $luceneDocument->pagetitle);
            $form->setValue('summary', $luceneDocument->summary);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }
    }

}