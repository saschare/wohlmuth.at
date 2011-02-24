<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Content_Edit {

	protected $doNotRegister = false;

	protected $regContents = array ();
	protected $regConfigs = array ();
	protected $noEdits = array ();

	protected $container = 0;

	protected $startTag = '';
	protected $endTag = '';

	protected $block = true;

	protected function __construct() {
	}

	protected static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function start($container) {

		if (!Aitsu_Registry :: isEdit() || self :: getInstance()->doNotRegister) {
			/*
			 * If we are not in edit mode, leave without notice.
			 */
			return;
		}

		$i = self :: getInstance();

		/*
		 * Set isBlock to true as a default value.
		 */
		$i->isBlock = true;

		/*
		 * Reset the internal registry.
		 */
		$i->regContents = array ();
		$i->regConfigs = array ();
		$i->container = $container;

		ob_start(array (
			'Aitsu_Content_Edit',
			'out'
		));
	}

	public static function end($isBlock = true) {

		if (!Aitsu_Registry :: isEdit() || self :: getInstance()->doNotRegister) {
			/*
			 * If we are not in edit mode, leave without notice.
			 */
			return;
		}

		Aitsu_Content_Edit :: isBlock($isBlock);

		ob_end_flush();
	}

	public static function out($buffer) {

		$i = self :: getInstance();

		if (Aitsu_Registry :: isEdit() && strlen(trim($buffer)) == 0) {
			$buffer = '[[ :: OUTPUT CONTAINER #' . $i->container . ' :: ]]';
		}

		if (Aitsu_Registry :: isEdit() == '1') {
			if (Aitsu_Content_Edit :: isBlock()) {
				$buffer = '<div id="container-' . $i->container . '-' . Aitsu_Registry :: get()->env->idartlang . '" class="aitsu_editable"><div class="aitsu_hover">' . $buffer . '</div></div>';
			} else {
				$buffer = '<span id="container-' . $i->container . '-' . Aitsu_Registry :: get()->env->idartlang . '" class="aitsu_editable"><span class="aitsu_hover">' . $buffer . '</span></span>';
			}
		}

		return $buffer;
	}

	public static function registerContent($content) {

		if (self :: getInstance()->doNotRegister) {
			return;
		}

		self :: getInstance()->regContents[] = $content;
	}

	public static function registerConfig($config) {

		if (self :: getInstance()->doNotRegister) {
			return;
		}

		self :: getInstance()->regConfigs[] = $config;
	}

	public static function getContents() {

		return self :: getInstance()->regContents;
	}

	public static function getConfigs() {

		return self :: getInstance()->regConfigs;
	}

	public static function register($do = true) {

		self :: getInstance()->doNotRegister = !$do;
	}

	public static function noEdit($method, $set = false) {

		$instance = self :: getInstance();

		$return = isset ($instance->noEdits[$method]) ? 1 : 0;

		if ($set) {
			$instance->noEdits[$method] = $method;
		}

		return $return;
	}

	public static function isBlock($isBlock = null) {

		$instance = self :: getInstance();

		if ($isBlock === null) {
			return $instance->block;
		}
		$instance->block = $isBlock;
	}
}