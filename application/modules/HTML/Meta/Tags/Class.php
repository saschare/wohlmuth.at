<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Module_HTML_Meta_Tags_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {

        $instance = new self();
        Aitsu_Content_Edit :: noEdit('HTML.Meta.Tags', true);

        $output = '';
        if ($instance->_get('HTML.Meta.Tags', $output)) {
            return $output;
        }

        $meta = Aitsu_Db::fetchRow("
                    SELECT
                        *
                    FROM
                        `_art_meta`
                    WHERE
                        `idartlang` =:idartlang",
                        array(
                            ':idartlang' => Aitsu_Registry :: get()->env->idartlang
                ));

        if (isset(Aitsu_Registry :: get()->config->honeytrap->keyword)) {
            $honeyTraps = array_flip(Aitsu_Registry :: get()->config->honeytrap->keyword->toArray());
            if (count(array_intersect_key($honeyTraps, $_GET)) > 0) {
                $meta['robots'] = (object) array(
                            'value' => 'noindex'
                );
            }
        }

        $view = $instance->_getView();
        $view->meta = $meta;

        $output = $view->render('index.phtml');

        $instance->_save($output, 'eternal');

        return $output;
    }

}