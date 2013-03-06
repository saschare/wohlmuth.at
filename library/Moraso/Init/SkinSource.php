<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Init_SkinSource implements Aitsu_Event_Listener_Interface {

    public static function notify(Aitsu_Event_Abstract $event) {

        if (!isset($_GET['skinsource'])) {
            return;
        }

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            $etag = $_SERVER['HTTP_IF_NONE_MATCH'];
        }

        $source = $_GET['skinsource'];
        if (substr($source, 0, 6) == 'admin/') {
            $source = substr($source, 6);
        }

        $exploded = explode("/", $source);

        $expires = 60 * 60 * 24 * 7;

        header("Pragma: public");
        header("Cache-Control: max-age=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        $file_extension = strtolower(pathinfo(end($exploded), PATHINFO_EXTENSION));

        self :: setContentTypeHeader($file_extension);

        if (isset($etag)) {
            $content = self :: _cache($etag);
        }

        if (isset($content) && $content) {
            header("HTTP/1.1 304 Not Modified");
            header("Connection: Close");
        } else {
            $content = self::_getContentFromSkinFile($source);

            if ($file_extension === 'css') {
                $etag = hash('md4', $content);
                self :: _cache($etag, $content, array(
                    'css'
                ));
            }

            if ($file_extension === 'js') {
                $etag = hash('md4', $content);
                self :: _cache($etag, $content, array(
                    'js'
                ));
            }

            if (!isset($etag)) {
                $etag = hash('md4', $content);
            }

            header("ETag: {$etag}");

            echo $content;
        }
        exit(0);
    }

    private static function _getContentFromSkinFile($source, $skin = null) {

        $skin = empty($skin) ? Aitsu_Registry :: get()->config->skin : $skin;

        $skin_path = APPLICATION_PATH . '/skins/' . $skin;

        $location = $skin_path . '/' . $source;

        if (is_readable($location)) {
            echo file_get_contents($location);
            return '';
        } else {
            $xml_file = $skin_path . '/skin.xml';

            if (is_readable($xml_file)) {
                $xml = simplexml_load_file($xml_file);

                self::_getContentFromSkinFile($source, (string) $xml->parent->skin[0]);
            } else {
                header("HTTP/1.1 404 Not Found");
                header("Connection: Close");
                exit();
            }
        }
    }

    private static function _cache($etag, $content = null, array $tags = null) {

        if (isset($content)) {
            Aitsu_Cache :: getInstance($etag)->save($content, $tags);
        } else {
            return Aitsu_Cache :: getInstance($etag)->load();
        }
    }

    private static function setContentTypeHeader($file_extension) {

        switch ($file_extension) {
            case 'css' :
                header('Content-type: text/css');
                break;
            case 'js' :
                header('Content-type: application/javascript');
                break;
            case 'png' :
                header('Content-type: image/png');
                break;
            case 'gif' :
                header('Content-type: image/gif');
                break;
            case 'jpg' :
                header('Content-type: image/jpeg');
                break;
            case 'jpeg' :
                header('Content-type: image/jpeg');
                break;
            case 'pdf' :
                header('Content-type: application/pdf');
                break;
            default :
                break;
        }
    }

}