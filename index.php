<?php

/**
 * @mainpage	aitsu content management
 * Welcome to the aitsu content management code repository.
 * aitsu is a very fast and flexible content management system and
 * application framework built on top of the ZEND framework.
 *
 * @section		Homepage
 * For more information please visit http://www.aitsu.org
 *
 * @section		Discussion Board
 * To find other people, discuss suggestions or problems please
 * visit the official bulletin board at http://www.aitsu.org/board
 *
 * @section		Source Control
 * aitsu has two projects on github
 *
 * 1. wdrei/aitsu is the core development project and will be
 *    under a strict release policy
 *
 * 2. aitsu/aitsu is the official development fork and will be
 *    updated more frequently and aims to provide more and current
 *    updates which may contain alpha and beta version code as well 
 *
 */

 /**
 * @category   aitsu
 * @package    aitsu_cms
 * @copyright  Copyright (c) 2009-2011
 * @license    see separate license.txt file
 *
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