<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

require_once 'Zend/Loader/Autoloader' . '.php';

class Aitsu_Cli_Bootstrap {

	protected function _initAutoloaders() {

		$autoloader = Zend_Loader_Autoloader :: getInstance();
		$libPath = realpath(APPLICATION_PATH . '/../library');
		$libs = scandir($libPath);
		foreach ($libs as $lib) {
			if (!in_array($lib, array (
					'.',
					'..',
					'Zend'
				)) && is_dir($libPath . '/' . $lib)) {
				$autoloader->registerNamespace($lib . '_');
			}
		}
	}

	protected function _initBackendConfig() {

		Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('backend');
	}

	protected function _initAppStatus() {

		Aitsu_Application_Status :: isEdit(true);
		Aitsu_Application_Status :: isPreview(false);
		Aitsu_Application_Status :: setEnv('backend');
		Aitsu_Application_Status :: lock();
	}

	protected function _initCli() {

		$options = getopt('u::p::s:');
		
		Aitsu_Registry :: get()->db = Zend_Db :: factory(Aitsu_Registry :: get()->config->database);
		
		if ($options['u'] != false) {
			Aitsu_Registry :: get()->config->database->params->username = $options['u'];
		}

		if ($options['p'] != false) {
			Aitsu_Registry :: get()->config->database->params->password = $options['p'];
		}

		if (!file_exists(APPLICATION_PATH . '/cli/scripts/' . $options['s'] . '.php')) {
			echo 'The script ' . $options['s'] . '.php does not exist.' . "\n";
			return;
		}
		
		include_once APPLICATION_PATH . '/cli/scripts/' . $options['s'] . '.php';

		$className = 'Cli_' . $options['s'];
		$script = new $className ();
		$script->execute();
	}

	public static function run() {

		static $running = false;

		if ($running) {
			throw new Exception('The bootstrap may only run once for each request.');
		}

		$instance = new self();

		try {
			$counter = 0;
			foreach (get_class_methods($instance) as $phase) {
				if (substr($phase, 0, strlen('_')) == '_') {
					call_user_func(array (
						$instance,
						$phase
					));
				}
				$counter++;
			}
		} catch (Exception $e) {
			trigger_error('Exception in ' . __FILE__ . ' on line ' . __LINE__ . ': ' . $e->getMessage());
			trigger_error("Stack trace: \n" . $e->getTraceAsString());
			exit ();
		}

		return $instance;
	}
}