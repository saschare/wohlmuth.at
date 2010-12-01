<?php


/**
 * @author Anreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

define('REQUEST_START', microtime(true));
$request = array_merge($_GET, $_POST, array (
	$_SERVER['HTTP_HOST']
));
ksort($request);
define('REQUEST_HASH', md5(serialize($request)));
unset ($request);
define('CACHE_PATH', realpath('./application/data/pagecache'));

require_once (realpath('./library/Aitsu/Bootstrap.php'));
$content = Aitsu_Bootstrap :: run()->getOutput();

$maxage = 10;

$etag = hash('md4', $content);
if (isset ($_SERVER['HTTP_IF_NONE_MATCH']) && $etag === $_SERVER['HTTP_IF_NONE_MATCH']) {
	header("Pragma: public");
	header("Cache-Control: max-age=" . $maxage);
	header("ETag: {$etag}");
	header("HTTP/1.1 304 Not Modified");
	header("Connection: Close");
	exit (0);
}

header("Pragma: public");
header("Cache-Control: max-age=" . $maxage);
header("ETag: {$etag}");
echo $content;
exit (0);