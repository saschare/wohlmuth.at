<?php


/**
 * Dynamic rewriting.
 *
 * @author Christian Kehres, webtischlerei.de
 * @copyright Copyright &copy; 2010, webtischlerei.de
 *
 * Aitsu_Core_Rewrite::create(array('idcat' => 12));
 *
 * {@id $Id: Rewrite.php 17690 2010-07-23 06:54:12Z akm $}
 */

class Aitsu_Core_Rewrite {

	protected $rewrite_class = null;

	protected function __construct() {

		$this->rewrite_class = call_user_func(Aitsu_Registry :: get()->config->rewrite->controller . '::getInstance');
	}

	protected static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public static function create(array $properties) {

		$instance = self :: getInstance();

		$query = http_build_query($properties);

		$url = $instance->rewrite_class->rewriteOutput('"front_content.php?' . $query . '"');

		return str_replace('"', '', $url);
	}
}