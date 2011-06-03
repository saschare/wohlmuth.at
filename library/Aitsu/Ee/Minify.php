<?php

class Aitsu_Ee_Minify {

    public static function minify($type, $source) {

        if ($type === 'js') {
            return Aitsu_Ee_JsMin::minify($source);
        }

        if ($type === 'css') {
            return Aitsu_Ee_CssMin::process($source);
        }
    }

}