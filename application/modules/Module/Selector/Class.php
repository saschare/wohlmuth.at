<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Module_Module_Selector_Class extends Aitsu_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {
        $selectionModules = Aitsu_Content_Config_Hidden :: set($this->_index, 'Module_Selector_params', $this->_params);

        $idartlang = Aitsu_Registry :: get()->env->idartlang;

        $keyValuePairs = array();
        $keys = array();
        $modules = array();

        $startTag = '';
        $code = '';
        $output = '';
        $endTag = '';

        if (isset($selectionModules->module)) {
            foreach ($selectionModules->module as $key => $line) {
                $keyValuePairs[$key] = $line->name;
                $keys[] = $key;
            }

            $modules = Aitsu_Content_Config_Module :: set($this->_index, 'Module_Selector', 'Module_Selector', $keyValuePairs, 'Modules');

            if (!empty($modules)) {
                foreach ($modules as $module) {
                    $output .= '_[' . $selectionModules->module->$module->module . ']';
                }
            }

            if (Aitsu_Registry :: isEdit()) {
                $startTag = '<div id="' . $this->_moduleName . '-' . $this->_index . '-' . $idartlang . '" class="aitsu_editable on-demand"><div class="aitsu_hover">';
                $startTag .= '<div class="show-on-demand" style="cursor:pointer; background-color:black; color:white; padding:10px; margin-bottom:5px; display:none;">selects Modules <strong>(' . $this->_index . ')</strong></div>';
                $endTag = '</div></div>';
            }

            if (Aitsu_Registry :: isBoxModel() && count($keys) > 1) {
                $startTag = '<shortcode method="' . $this->_moduleName . '" index="' . $this->_index . '">';
                $startTag .= 'isEdit: ' . var_export(Aitsu_Registry :: isEdit(), true);
                $endTag = '</shortcode>';
            }

            if ((Aitsu_Registry :: isEdit() || Aitsu_Registry :: get()->env->editAction == '1') && count($keys) > 1) {
                $parameters = str_replace("\n", '\n', str_replace("\r\n", "\n", $this->_context['params']));
                $code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
            }
        }

        return $startTag . $code . $output . $endTag;
    }

}