<?php

/**
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2011, webtischlerei
 */
class Module_Image_Class extends Aitsu_Ee_Module_Abstract {

    public static function init($context) {

        $instance = new self();

        $index = empty($context['index']) ? 'noindex' : $context['index'];
        $params = Aitsu_Util :: parseSimpleIni($context['params']);

        $template = empty($params->template) ? 'index' : $params->template;

        $images = Aitsu_Content_Config_Media::set($index, 'media', 'Media');

        $view = $instance->_getView();
        $view->images = Aitsu_Persistence_View_Media::byFileName(Aitsu_Registry::get()->env->idart, $images);
        
        $output = $view->render($template . '.phtml');
        return $output;
    }

}