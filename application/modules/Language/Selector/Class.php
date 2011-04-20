<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Module_Language_Selector_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {

        Aitsu_Content_Edit :: noEdit('Language.Selector', true);

        $instance = new self();

        $view = $instance->_getView();

        $languages = Aitsu_Db::fetchAll("
            SELECT
                `idlang`,
                `name`
            FROM
                `_lang`
            WHERE
                `idclient` =:idclient",
                        array(
                            ':idclient' => Aitsu_Config::get('sys.client')
                        )
        );
        
        foreach ($languages as $language) {
            $language = (object) $language;
            Aitsu_Core_Navigation_Language::getInstance()->registerLang($language->idlang, $language->name);
        }

        $view->langs = Aitsu_Core_Navigation_Language::getInstance()->getLangs();

        $output = $view->render('index.phtml');

        return $output;
    }

}

?>
