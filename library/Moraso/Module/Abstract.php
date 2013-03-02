<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
abstract class Moraso_Module_Abstract extends Aitsu_Module_Abstract {

    protected function _getView($view = null) {

        if ($this->_view != null) {
            return $this->_view;
        }

        $view = new Zend_View();

        $module_parts = explode('_', get_class($this));

        if ($module_parts[0] != 'Module') {
            unset($module_parts[0]);
        }

        unset(end($module_parts));

        $modulePath = implode('/', $module_parts);

        echo 'Es handelt sich um das Modul ' . $modulePath . '!';
        exit();

        /**
         * @todo Nun schauen wir wo sich ein Template dazu befindet und setzen den Pfad dementsprechend
         * 
         * @todo erster Check: prüfen ob das Skin dieses Modul beinhaltet
         * @todo zweiter Check: handelt es sich um ein moraso Modul
         * @todo dritter Check: handelt es sich um ein aitsu Modul
         * @todo vierter Check: nun frage ich andere Ordner in der Library ab, unzwar alle außer die Ordner die "default" vorhanden sind
         */

        return $view;
    }

}