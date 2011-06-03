<?php

/**
 * Add additional skin informations to layout
 *
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 *
 * {@id$}
 */
class Aitsu_Ee_Transformation_Skin implements Aitsu_Event_Listener_Interface {

    protected function __construct() {

    }

    public static function getInstance() {

        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public static function notify(Aitsu_Event_Abstract $event) {
    	
    	if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

        $event->bootstrap->pageContent = str_replace('="image/', '="/image/', $event->bootstrap->pageContent);

        $event->bootstrap->pageContent = preg_replace('@="(css|js|images|gfx)@', "=\"/skin/$1", $event->bootstrap->pageContent);
        $event->bootstrap->pageContent = preg_replace('@="\/(css|js|images|gfx)@', "=\"/skin/$1", $event->bootstrap->pageContent);
        $event->bootstrap->pageContent = preg_replace('@="\./(css|js|images|gfx)@', "=\"/skin/$1", $event->bootstrap->pageContent);
    }

}