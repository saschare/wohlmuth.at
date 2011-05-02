<?php

/**
 * @author Anreas Kummer, w3concepts AG
 * @author Christian Kehres, webtischlerei
 * @copyright Copyright &copy; 2010, w3concepts AG
 * @copyright Copyright &copy; 2011, webtischlerei
 */
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

define('REQUEST_START', microtime(true));
$request = array_merge($_GET, $_POST, array(
            $_SERVER['HTTP_HOST']
        ));
ksort($request);
define('REQUEST_HASH', hash('md4', serialize($request)));
unset($request);
define('CACHE_PATH', APPLICATION_PATH . '/data/pagecache');

require_once (realpath('./library/Aitsu/Bootstrap.php'));
$content = Aitsu_Bootstrap :: run()->getOutput();
$etag = hash('md4', $content);

header("ETag: {$etag}");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === $_SERVER['HTTP_IF_NONE_MATCH']) {
    header("HTTP/1.1 304 Not Modified");
    header("Connection: Close");
    exit(0);
}

echo $content;
exit(0);