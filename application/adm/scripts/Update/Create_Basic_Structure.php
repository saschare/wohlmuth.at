<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Adm_Script_Create_Basic_Structure extends Aitsu_Adm_Script_Abstract {

    public static function getName() {

        return Aitsu_Translate::translate('Create Basic Structure');
    }

    public function doInitCreation() {

        try {
            Aitsu_Registry::get()->session->createStructure->idlang = Aitsu_Registry::get()->session->currentLanguage;
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Initialization failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Initialization has been done')));
    }

    public function _createCategory($catInfo, $createInIdCat = 0) {
        
        Aitsu_Db::startTransaction();
        
        $idcat = Aitsu_Persistence_Category::factory($createInIdCat)->insert(Aitsu_Registry::get()->session->createStructure->idlang);
        
        $category = Aitsu_Persistence_Category::factory($idcat);
        $category->load();

        $catInfo['visible'] = 1;
        
        $category->setValues($catInfo);

        $category->save();

        Aitsu_Db::commit();

        return $idcat;
    }

    public function _createArticle($title, $pagetitle, $idcat) {

        Aitsu_Db::startTransaction();

        $art = Aitsu_Persistence_Article::factory();
        $art->title = $title;
        $art->pagetitle = $pagetitle;
        $art->online = 1;
        $art->idclient = Aitsu_Registry::get()->session->currentClient;
        $art->idcat = $idcat;
        $art->save();

        $art->setAsIndex();

        Aitsu_Db::commit();

        return $art->idart;
    }

    public function doCreateMainCategory() {

        try {
            $catInfo = array(
                'name' => 'main',
                'urlname' => 'main'
            );

            Aitsu_Registry::get()->session->createStructure->mainIdCat = $this->_createCategory($catInfo);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Main Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Main Category has been created')));
    }

    public function doCreateMetaCategory() {

        try {
            $catInfo = array(
                'name' => 'meta',
                'urlname' => 'meta'
            );

            Aitsu_Registry::get()->session->createStructure->metaIdCat = $this->_createCategory($catInfo);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Meta Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Meta Category has been created')));
    }

    public function doCreateSystemCategory() {

        try {
            $catInfo = array(
                'name' => 'system',
                'urlname' => 'system'
            );

            Aitsu_Registry::get()->session->createStructure->systemIdCat = $this->_createCategory($catInfo);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating System Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('System Category has been created')));
    }

    public function doCreateImprintCategory() {

        try {
            $catInfo = array(
                'name' => 'Impressum',
                'urlname' => 'impressum'
            );

            Aitsu_Registry::get()->session->createStructure->imprintIdCat = $this->_createCategory($catInfo, Aitsu_Registry::get()->session->createStructure->metaIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Imprint Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Imprint Category has been created')));
    }

    public function doCreateLoginCategory() {

        try {
            $catInfo = array(
                'name' => 'Login',
                'urlname' => 'login'
            );

            Aitsu_Registry::get()->session->createStructure->loginIdCat = $this->_createCategory($catInfo, Aitsu_Registry::get()->session->createStructure->systemIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Login Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Login Category has been created')));
    }

    public function doCreateErrorCategory() {

        try {
            $catInfo = array(
                'name' => 'Error',
                'urlname' => 'error'
            );

            Aitsu_Registry::get()->session->createStructure->errorIdCat = $this->_createCategory($catInfo, Aitsu_Registry::get()->session->createStructure->systemIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Error Category failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Error Category has been created')));
    }

    public function doCreateStartArticle() {

        try {
            Aitsu_Registry::get()->session->createStructure->startIdArt = $this->_createArticle('index', 'Herzlich willkommen', Aitsu_Registry::get()->session->createStructure->mainIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Start Article failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Start Article has been created')));
    }

    public function doCreateErrorArticle() {

        try {
            Aitsu_Registry::get()->session->createStructure->errorIdArt = $this->_createArticle('index', 'Seite nicht gefunden', Aitsu_Registry::get()->session->createStructure->errorIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Error Article failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Error Article has been created')));
    }
    
    public function doCreateLoginArticle() {

        try {
            Aitsu_Registry::get()->session->createStructure->loginIdArt = $this->_createArticle('index', 'Login' , Aitsu_Registry::get()->session->createStructure->loginIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Login Article failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Login Article has been created')));
    }
    
    public function doCreateImprintArticle() {

        try {
            Aitsu_Registry::get()->session->createStructure->imprintIdArt = $this->_createArticle('index', 'Impressum', Aitsu_Registry::get()->session->createStructure->imprintIdCat);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
            return (object) array(
                        'message' => Aitsu_Translate::translate('Creating Imprint Article failed')
            );
        }

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Imprint Article has been created')));
    }
    
    public function doWriteConfig() {

        Aitsu_Db::startTransaction();

        $client = 'default';
        $env = 'live';

        $config = Moraso_Persistence_Config::factory();

        $config->setValue($client, $env, 'sys.errorpage', Aitsu_Registry::get()->session->createStructure->errorIdArt);
        $config->setValue($client, $env, 'sys.startcat', Aitsu_Registry::get()->session->createStructure->mainIdCat);
        $config->setValue($client, $env, 'sys.loginpage', Aitsu_Registry::get()->session->createStructure->loginIdCat);
        $config->setValue($client, $env, 'navigation.main', Aitsu_Registry::get()->session->createStructure->mainIdCat);
        $config->setValue($client, $env, 'navigation.meta', Aitsu_Registry::get()->session->createStructure->metanIdCat);

        $config->save();

        Aitsu_Db::commit();

        return Aitsu_Adm_Script_Response :: factory((Aitsu_Translate::translate('Config has been written')));
    }
    
    public function doFinished() {

        return Aitsu_Adm_Script_Response :: factory(Aitsu_Translate::translate('Script finished!'));
    }

}