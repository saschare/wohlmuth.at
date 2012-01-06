<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_ModuleSelector_Class extends Aitsu_Module_Abstract {

    protected function _main() {
        $selectionModules = Aitsu_Content_Config_Hidden :: set($this->_index, 'ModuleSelector_params', $this->_params);

        $keyValuePairs = array();
        $keys = array();
        $modules = array();

        $output = '';

        if (isset($selectionModules->module)) {
            foreach ($selectionModules->module as $key => $line) {
                $keyValuePairs[$key] = $line->name;
                $keys[] = $key;
            }

            $modules = Aitsu_Content_Config_Module :: set($this->_index, 'ModuleSelector', 'ModuleSelector', $keyValuePairs, 'Modules');

            if (empty($modules)) {
                return '';
            }

            foreach ($modules as $module) {
                $output .= '_[' . $selectionModules->module->$module->module . ']';
            }
        }

        return $output;
    }

}