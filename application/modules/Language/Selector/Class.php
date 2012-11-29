<?php

/**
 * @author Andreas Kummer <info@wdrei.ch>
 * @copyright (c) 2011, w3concepts AG <http://www.wdrei.ch>
 * 
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 */
class Module_Language_Selector_Class extends Aitsu_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();

        $languages = Moraso_Db::fetchAll('' .
                        'select ' .
                        '   idlang, ' .
                        '   name, ' .
                        '   longname ' .
                        'from ' .
                        '   _lang ' .
                        'where ' .
                        '   idclient = :idclient', array(
                    ':idclient' => Aitsu_Config::get('sys.client')
                ));

        $languageInstance = Aitsu_Core_Navigation_Language::getInstance();
        
        foreach ($languages as $language) {
            $display = empty($language['longname']) ? $language['name'] : $language['longname'];
            
            $languageInstance->registerLang($language['idlang'], $language['name'], $display);
        }

        $view->langs = $languageInstance->getLangs();

        return $view->render('index.phtml');
    }

}