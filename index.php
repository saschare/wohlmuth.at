<?php

/**
 * @author Andreas Kummer <a.kummer@wdrei.ch>
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * 
 * @copyright (c) 2012, w3concepts AG <http://www.wdrei.ch>
 * @copyright (c) 2012, webtischlerei <http://www.webtischlerei.de>
 * 
 * @version 1.1
 * @since 2.4.5
 */

define('REQUEST_START', microtime(true));

$dirPath = dirname(__FILE__);

define('APPLICATION_PATH', $dirPath. '/application');
define('LIBRARY_PATH', $dirPath. '/library');
define('CACHE_PATH', APPLICATION_PATH . '/data/pagecache');

require_once (LIBRARY_PATH . '/Aitsu/Bootstrap.php');

$request = array_merge($_REQUEST, array($_SERVER['HTTP_HOST']));
$serializedRequest = serialize($request);
define('REQUEST_HASH', crc32($serializedRequest));
unset($request);

$content = Aitsu_Bootstrap :: run()->getOutput();

$etag = crc32($content);

header("ETag: {$etag}");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === $_SERVER['HTTP_IF_NONE_MATCH']) {
    header("HTTP/1.1 304 Not Modified");
    header("Connection: Close");
    exit(0);
}

echo $content;
exit(0);