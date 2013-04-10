<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Module_Selector_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {
        $selectionModules = Aitsu_Content_Config_Hidden::set($this->_index, 'Module_Selector_params', $this->_params);

        $keyValuePairs = array();
        $keys = array();
        $modules = array();

        $output = '';

        if (isset($selectionModules->module)) {
            foreach ($selectionModules->module as $key => $line) {
                $keyValuePairs[$key] = $line->name;
                $keys[] = $key;
            }

            $modules = Aitsu_Content_Config_Module::set($this->_index, 'Module_Selector', 'Module_Selector', $keyValuePairs, 'Modules');

            foreach ($modules as $module) {
                $output .= '_[' . $selectionModules->module->$module->module . ']';
            }

            if (Aitsu_Registry::isEdit()) {
                $startTag = '<div id="' . $this->_moduleName . '-' . $this->_index . '-' . Aitsu_Registry::get()->env->idartlang . '" class="aitsu_editable on-demand"><div class="aitsu_hover">';
                $startTag .= '<div class="show-on-demand" style="cursor:pointer; background-color:black; color:white; padding:10px; margin-bottom:5px; display:none;">Edit ' . $this->_moduleName . ' <strong>' . $this->_index . '</strong></div>';
                $endTag = '</div></div>';
            }

            if (Aitsu_Registry::isBoxModel() && count($keys) > 1) {
                $startTag = '<shortcode method="' . $this->_moduleName . '" index="' . $this->_index . '">';
                $startTag .= 'isEdit: ' . var_export(Aitsu_Registry::isEdit(), true);
                $endTag = '</shortcode>';
            }

            if ((Aitsu_Registry::isEdit() || Aitsu_Registry::get()->env->editAction == '1') && count($keys) > 1) {
                $parameters = str_replace("\n", '\n', str_replace("\r\n", "\n", $this->_context['params']));
                $code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
            }
        }

        if (Aitsu_Registry::isEdit() || (Aitsu_Registry::isBoxModel() && count($keys) > 1)) {
            return $startTag . $code . $output . $endTag;
        }

        return $output;
    }

}