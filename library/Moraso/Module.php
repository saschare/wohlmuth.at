<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module {

    protected $container;
    protected $context;
    protected $output = null;
    protected $shortCode = null;

    protected function __construct($idart, $idlang, $container, $shortCode) {

        $this->shortCode = $shortCode;
        $this->container = $container;
        $this->context = Moraso_Module_Context::get($idart, $idlang);
    }

    public static function factory($idart, $container, $idlang = null, $shortCode = null) {

        if (empty($idlang)) {
            $idlang = Aitsu_Registry::get()->session->currentClient;
        }

        return new self($idart, $idlang, $container, $shortCode);
    }

    public function getOutput($renderShortCodes = false, $edit = '0', $index = null, $params = null) {

        if ($this->output != null) {
            return $this->output;
        }

        ob_start();

        $old['config'] = Aitsu_Registry::get()->config;
        $old['idart'] = Aitsu_Registry::get()->env->idart;
        $old['idartlang'] = Aitsu_Registry::get()->env->idartlang;
        $old['idlang'] = Aitsu_Registry::get()->env->idlang;
        $old['idcat'] = Aitsu_Registry::get()->env->idcat;
        $old['client'] = Aitsu_Registry::get()->env->client;
        $old['edit'] = Aitsu_Registry::get()->env->edit;

        Aitsu_Registry::get()->config = $this->context['config'];
        Aitsu_Registry::get()->env->idart = $this->context['idart'];
        Aitsu_Registry::get()->env->idartlang = $this->context['idartlang'];
        Aitsu_Registry::get()->env->idlang = $this->context['idlang'];
        Aitsu_Registry::get()->env->idcat = $this->context['idcat'];
        Aitsu_Registry::get()->env->client = $this->context['client'];
        Aitsu_Registry::get()->env->edit = $edit;

        foreach ($this->context as $key => $value) {
            $$key = $value;
        }

        if ($this->shortCode != null) {
            $return = Moraso_Shortcode::getInstance()->evalModule($this->shortCode, $params, 0, $index);
        }

        if ($renderShortCodes) {
            $return = Moraso_Transformation_Shortcode::getInstance()->getContent($return);
        }

        Aitsu_Registry::get()->config = $old['config'];
        Aitsu_Registry::get()->env->idart = $old['idart'];
        Aitsu_Registry::get()->env->idartlang = $old['idartlang'];
        Aitsu_Registry::get()->env->idlang = $old['idlang'];
        Aitsu_Registry::get()->env->idcat = $old['idcat'];
        Aitsu_Registry::get()->env->client = $old['client'];
        Aitsu_Registry::get()->env->edit = $old['edit'];

        return $return;
    }

    public function getHelp() {

        $modulePath = str_replace('.', '/', $this->shortCode);

        $heredity = Moraso_Skin_Heredity::build();

        foreach ($heredity as $skin) {
            $files['Skin_Module'][] = APPLICATION_PATH . "/skins/" . $skin . "/module/" . $modulePath . '/Class.php';
        }

        $files['Moraso_Module'] = realpath(APPLICATION_PATH . '/../library/') . '/Moraso/Module/' . $modulePath . '/Class.php';
        $files['Module'] = APPLICATION_PATH . '/modules/' . $modulePath . '/Class.php';

        $exists = false;

        $profileDetails = new stdClass();

        foreach ($files as $prefix => $file) {
            if (is_array($file)) {
                foreach ($file as $path) {
                    if (file_exists($path)) {
                        $exists = true;
                        $profileDetails->source = $prefix . '_' . str_replace('.', '_', $this->shortCode) . '_Class';
                        include_once $path;
                        if (method_exists($profileDetails->source, 'help')) {
                            return call_user_func(array(
                                $profileDetails->source,
                                'help'
                            ));
                        }
                        break;
                    }
                }
                if ($exists) {
                    break;
                }
            } else {
                if (file_exists($file)) {
                    $exists = true;
                    $profileDetails->source = $prefix . '_' . str_replace('.', '_', $this->shortCode) . '_Class';
                    include_once $file;
                    if (method_exists($profileDetails->source, 'help')) {
                        return call_user_func(array(
                            $profileDetails->source,
                            'help'
                        ));
                    }
                    break;
                }
            }
        }

        return null;
    }

}