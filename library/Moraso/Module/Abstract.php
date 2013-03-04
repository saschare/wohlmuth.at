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
        
        $module_sliced = array_slice($module_parts, $module_parts[0] != 'Module' ? 2 : 1, -1);

        $modulePath = implode('/', $module_sliced);

        $modulePaths = array(
            'skin' => APPLICATION_PATH . "/skins/" . Aitsu_Config::get('skin') . "/module/" . $modulePath . '/',
            'moraso' => LIBRARY_PATH . '/Moraso/Module/' . $modulePath . '/',
            'aitsu' => APPLICATION_PATH . '/modules/' . $modulePath . '/'
        );
        
        foreach ($modulePaths as $path) {
            if (count(glob($path . '*.phtml')) > 0) {
                $view->setScriptPath($path);
                break;
            }
        }

        return $view;
    }

}