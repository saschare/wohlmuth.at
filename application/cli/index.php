<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
define('APPLICATION_ENV', 'development');
define('APPLICATION_LIBPATH', realpath(APPLICATION_PATH . '/../library'));

set_include_path(implode(PATH_SEPARATOR, array (
	realpath(APPLICATION_PATH . '/../library'),
	get_include_path()
)));
set_include_path(implode(PATH_SEPARATOR, array (
	realpath(APPLICATION_PATH . '/..'),
	get_include_path()
)));

require_once 'Aitsu/Cli/Bootstrap' . '.php';

Aitsu_Cli_Bootstrap :: run();
