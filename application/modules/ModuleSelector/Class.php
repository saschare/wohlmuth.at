<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
 
class Module_ModuleSelector_Class extends Aitsu_Module_Abstract {

    protected function _init($context) {

        $params = Aitsu_Content_Config_Hidden :: set($this->_index, 'ModuleSelector_params', $this->_params);
        $idartlang = Aitsu_Registry :: get()->env->idartlang;
        $keys = array();

        if (isset($params->module)) {

            $data = (array) $params->module;

            foreach ($data as $key => $line) {
                $keyValuePairs[$key] = $line->name;
                $keys[] = $key;
            }

            $modules = Aitsu_Content_Config_Module :: set($this->index, 'ModuleSelector', 'ModuleSelector', $keyValuePairs, 'Modules');

            if (Aitsu_Registry :: isEdit()) {
                $startTag = '<div id="Template-' . $this->_index . '-' . $idartlang . '" class="aitsu_editable on-demand"><div class="aitsu_hover">';
                $startTag .= '<div class="show-on-demand" style="cursor:pointer; background-color:black; color:white; padding:10px; margin-bottom:5px; display:none;">Module Selector area <strong>' . $this->_index . '</strong></div>';
                $endTag = '</div></div>';
            }

            if (Aitsu_Registry :: isBoxModel() && count($keys) > 1) {
                $startTag = '<shortcode method="ModuleSelector" index="' . $this->_index . '">';
                $startTag .= 'isEdit: ' . var_export(Aitsu_Registry :: isEdit(), true);
                $endTag = '</shortcode>';
            }
        }

        $code = '';

        if ((Aitsu_Registry :: isEdit() || Aitsu_Registry :: get()->env->editAction == '1') && count($keys) > 1) {
            $parameters = str_replace("\n", '\n', str_replace("\r\n", "\n", $this->_context['params']));
            $code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
        }

        $output = '';
        foreach ($modules as $module) {
            $output .= '_[' . $params->module->$module->module . ']';
        }

        return $code . $output;
    }

}