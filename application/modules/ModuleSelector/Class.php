<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Module_ModuleSelector_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {

        $index = str_replace('_', ' ', $context['index']);
        $parameters = ($context['params'] === null) ? null : Aitsu_Util :: parseSimpleIni($context['params']);
        $params = Aitsu_Content_Config_Hidden :: set($index, 'ModuleSelector_params', $parameters);

        $idartlang = Aitsu_Registry :: get()->env->idartlang;

        $keys = array();

        if (isset($params->module)) {

            $data = (array) $params->module;

            foreach ($data as $key => $line) {
                $keyValuePairs[$key] = $line->name;
                $keys[] = $key;
            }

            $modules = Aitsu_Content_Config_Module :: set($index, 'ModuleSelector', 'ModuleSelector', $keyValuePairs, 'Modules');

            if (Aitsu_Registry :: isEdit()) {
                $startTag = '<div id="Template-' . $index . '-' . $idartlang . '" class="aitsu_editable on-demand"><div class="aitsu_hover">';
                $startTag .= '<div class="show-on-demand" style="cursor:pointer; background-color:black; color:white; padding:10px; margin-bottom:5px; display:none;">Module Selector area <strong>' . $index . '</strong></div>';
                $endTag = '</div></div>';
            }

            if (Aitsu_Registry :: isBoxModel() && count($keys) > 1) {
                $startTag = '<shortcode method="ModuleSelector" index="' . $index . '">';
                $startTag .= 'isEdit: ' . var_export(Aitsu_Registry :: isEdit(), true);
                $endTag = '</shortcode>';
            }
        }

        $code = '';

        if ((Aitsu_Registry :: isEdit() || Aitsu_Registry :: get()->env->editAction == '1') && count($keys) > 1) {
            $parameters = str_replace("\n", '\n', str_replace("\r\n", "\n", $context['params']));
            $code = '<code class="aitsu_params" style="display:none;">' . $parameters . '</code>';
        }

        $output = '';
        foreach ($modules as $module) {
            $output .= '_[' . $params->module->$module->module . ']';
        }

        return $code . $output;
    }

}