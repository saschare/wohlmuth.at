<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class luceneAnalyserDashboardController extends Aitsu_Adm_Plugin_Controller {

    const ID = '515c5162-69dc-488f-b3aa-2f77c0a8b230';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        $client_data = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient);

        $client_config = new Zend_Config_Ini('application/configs/clients/' . $client_data->config . '.ini', Moraso_Util::getEnv());

        return (object) array(
                    'name' => 'luceneAnalyser',
                    'tabname' => Aitsu_Translate :: _('luceneAnalyser'),
                    'enabled' => isset($client_config->search->lucene->index) ? true : false,
                    'id' => self :: ID
        );
    }

    public function indexAction() {
        
    }

    public function storeAction() {

        $articles = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   lastindexed, ' .
                        '   idart, ' .
                        '   idlang ' .
                        'from ' .
                        '   _lucene_index ' .
                        'where ' .
                        '   idlang =:idlang ' .
                        'order by ' .
                        '   lastindexed DESC', array(
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
        ));

        $client_data = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient);

        $client_config = new Zend_Config_Ini('application/configs/clients/' . $client_data->config . '.ini', Moraso_Util::getEnv());

        $index = Zend_Search_Lucene::open(APPLICATION_PATH . '/data/lucene/' . $client_config->search->lucene->index . '/');
        
        foreach ($articles as $key => $article) {
            $article = (object) $article;

            $hits = $index->find('uid:' . $article->idart . '-' . $article->idlang . ' AND lang:' . $article->idlang . ' AND idart:' . $article->idart);
                        
            if ($hits[0]->score == 1) {
                $luceneDocument = $hits[0]->getDocument();

                $articles[$key]['id'] = $hits[0]->id;
                $articles[$key]['uid'] = $hits[0]->uid;

                $articles[$key]['pagetitle'] = $luceneDocument->pagetitle;
                $articles[$key]['summary'] = $luceneDocument->summary;
            }

            unset($hits);
        }

        $this->_helper->json((object) array(
                    'articles' => $articles,
                    'count' => $index->count(),
                    'numDocs' => $index->numDocs(),
                    'maxDoc' => $index->maxDoc()
        ));
    }

    public function optimizeAction() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");

        $client_data = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient);

        $client_config = new Zend_Config_Ini('application/configs/clients/' . $client_data->config . '.ini', Moraso_Util::getEnv());

        $index = new Zend_Search_Lucene(APPLICATION_PATH . '/data/lucene/' . $client_config->search->lucene->index . '/');

        $index->optimize();

        $this->_helper->json((object) array());
    }

    public function deleteAction() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
        
        $uid = $this->getRequest()->getParam('uid');
        $id = $this->getRequest()->getParam('id');

        $client_data = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient);

        $client_config = new Zend_Config_Ini('application/configs/clients/' . $client_data->config . '.ini', Moraso_Util::getEnv());

        $index = new Zend_Search_Lucene(APPLICATION_PATH . '/data/lucene/' . $client_config->search->lucene->index . '/');

        $index->delete($id);

        Moraso_Db::query('' .
                'delete from ' .
                '   _lucene_index ' .
                'where ' .
                '   uid =:uid', array(
            ':uid' => $uid
        ));

        $this->_helper->json((object) array());
    }

    public function deletebrokenAction() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
        
        $articles = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   lastindexed, ' .
                        '   idart, ' .
                        '   idlang ' .
                        'from ' .
                        '   _lucene_index ' .
                        'where ' .
                        '   idlang =:idlang ' .
                        'order by ' .
                        '   lastindexed DESC', array(
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
        ));

        $client_data = Aitsu_Persistence_Clients::factory(Aitsu_Registry::get()->session->currentClient);

        $client_config = new Zend_Config_Ini('application/configs/clients/' . $client_data->config . '.ini', Moraso_Util::getEnv());

        $index = new Zend_Search_Lucene(APPLICATION_PATH . '/data/lucene/' . $client_config->search->lucene->index . '/');

        foreach ($articles as $article) {
            $article = (object) $article;

            $hits = $index->find('uid:' . $article->idart . '-' . $article->idlang . ' AND lang:' . $article->idlang . ' AND idart:' . $article->idart);
            
            trigger_error('idart' . $article->idart . ' with score ' . $hits[0]->score);
            
            if ($hits[0]->score == 1) {
                $id = $hits[0]->id;
                $uid = $hits[0]->uid;
                
                $luceneDocument = $hits[0]->getDocument();
                $pagetitle = $luceneDocument->pagetitle;
                                
                if (empty($pagetitle)) {
                    
                    trigger_error('delete lucene index extry id ' . $id);
                    
                    $index->delete($id);

                    Moraso_Db::query('' .
                            'delete from ' .
                            '   _lucene_index ' .
                            'where ' .
                            '   uid =:uid', array(
                        ':uid' => $uid
                    ));
                }
            }

            unset($hits);
        }

        $this->_helper->json((object) array());
    }

}