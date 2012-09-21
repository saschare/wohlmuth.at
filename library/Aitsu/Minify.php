<?php

class Aitsu_Minify {

    public static function minify($type, $source) {

        if ($type === 'js') {
            return Aitsu_JsMin::minify($source);
        }

        if ($type === 'css') {
            return Aitsu_CssMin::process($source);
        }
    }

}