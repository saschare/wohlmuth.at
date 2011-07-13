<?php


/**
 * Module rendering.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Module.php 19236 2010-10-08 15:30:11Z akm $}
 */

class Aitsu_Core_Module {

	protected $idartlang;
	protected $container;
	protected $context;
	protected $output = null;
	protected $shortCode = null;

	protected function __construct($idart, $idlang, $container, $shortCode) {

		if ($idlang == null) {
			/*
			 * Idart is assumed to be idartlang.
			 */
			$this->idartlang = $idart;
		} else {
			$this->idartlang = Aitsu_Db :: fetchOne('' .
			'select idartlang from _art_lang where idart = ? and idlang = ? ', array (
				$idart,
				$idlang
			));
		}

		$this->shortCode = $shortCode;
		$this->container = $container;

		$this->context = Aitsu_Core_Module_Context :: get($this->idartlang);
	}

	public static function factory($idart, $container, $idlang = null, $shortCode = null) {

		/*
		 * If idlang is null, the variable idart is assumed to be idartlang.
		 */

		$instance = new self($idart, $idlang, $container, $shortCode);

		return $instance;
	}

	public function getOutput($renderShortCodes = false, $edit = '0', $index = null, $params = null) {

		if ($this->output != null) {
			return $this->output;
		}

		ob_start();

		$old['idartlang'] = Aitsu_Registry :: get()->env->idartlang;
		$old['idart'] = Aitsu_Registry :: get()->env->idart;
		$old['idlang'] = Aitsu_Registry :: get()->env->idlang;
		$old['idcat'] = Aitsu_Registry :: get()->env->idcat;
		$old['client'] = Aitsu_Registry :: get()->env->client;
		$old['edit'] = Aitsu_Registry :: get()->env->edit;

		Aitsu_Registry :: get()->config = $this->context['config'];
		Aitsu_Registry :: get()->env->idart = $this->context['idart'];
		Aitsu_Registry :: get()->env->idartlang = $this->context['idartlang'];
		Aitsu_Registry :: get()->env->idlang = $this->context['idlang'];
		Aitsu_Registry :: get()->env->idcat = $this->context['idcat'];
		Aitsu_Registry :: get()->env->client = $this->context['client'];
		Aitsu_Registry :: get()->env->edit = $edit;

		foreach ($this->context as $key => $value) {
			$$key = $value;
		}

		$cCurrentContainer = $this->container;

		if ($this->shortCode != null) {
			$return = Aitsu_Ee_Shortcode :: getInstance()->evalModule($this->shortCode, $params, 0, $index);
		}
		if ($renderShortCodes) {
			$return = Aitsu_Ee_Transformation_Shortcode :: getInstance()->getContent($return);
		}

		/*
		 * Restore registry.
		 */
		Aitsu_Registry :: get()->env->idartlang = $old['idartlang'];
		Aitsu_Registry :: get()->env->idart = $old['idart'];
		Aitsu_Registry :: get()->env->idlang = $old['idlang'];
		Aitsu_Registry :: get()->env->client = $old['client'];
		Aitsu_Registry :: get()->env->edit = $old['edit'];

		return $return;
	}

	public function getHelp() {

		$files = array (
			'Skin_Module' => APPLICATION_PATH . "/skins/" . Aitsu_Registry :: get()->config->skin . "/module/" . str_replace('.', '/', $this->shortCode) . "/Class.php",
			'Local_Module' => realpath(APPLICATION_PATH . '/../library/Local/Module/' . str_replace('.', '/', $this->shortCode) . '/Class.php'),
			'Comm_Module' => realpath(APPLICATION_PATH . '/../library/Comm/Module/' . str_replace('.', '/', $this->shortCode) . '/Class.php'),
			'Module' => APPLICATION_PATH . '/modules/' . str_replace('.', '/', $this->shortCode) . '/Class.php',
			'Aitsu_Ee_Module' => realpath(APPLICATION_PATH . '/../library/Aitsu/Ee/Module/' . str_replace('.', '/', $this->shortCode) . '/Class.php')
		);

		$exists = false;

		foreach ($files as $prefix => $file) {
			if (file_exists($file)) {
				$exists = true;
				$profileDetails->source = $prefix . '_' . str_replace('.', '_', $this->shortCode) . '_Class';
				include_once $file;
				if (method_exists($profileDetails->source, 'help')) {
					return call_user_func(array (
						$profileDetails->source,
						'help'
					));
				}
				break;
			}
		}

		return null;
	}
}