<?php

/**
 * @author Anreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
define('REQUEST_START', microtime(true));
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
define('CACHE_PATH', APPLICATION_PATH . '/data/pagecache');

require_once (realpath('./library/Aitsu/Bootstrap.php'));

$md5available = in_array('md5', hash_algos()) ? true : false;

$request = array_merge($_REQUEST, array($_SERVER['HTTP_HOST']));
$serializedRequest = serialize($request);
define('REQUEST_HASH', $md5available ? md5($serializedRequest) : hash('md4', $serializedRequest));
unset($request);

$content = Aitsu_Bootstrap :: run()->getOutput();

$etag = $md5available ? md5($content) : hash('md4', $content);

header("ETag: {$etag}");

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === $_SERVER['HTTP_IF_NONE_MATCH']) {
    header("HTTP/1.1 304 Not Modified");
    header("Connection: Close");
    exit(0);
}

echo $content;
exit(0);