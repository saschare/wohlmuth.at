<?php


/**
 * Abstract article plugin class.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 16018 2010-04-21 12:06:37Z akm $}
 */

abstract class Aitsu_Core_Plugin_Article_Abstract implements Aitsu_Core_Plugin_Article_Interface {
	
	protected $id;
	protected $view;

	abstract protected function __construct();

	public static function _getInstance() {
		
		return;
	}

	public final function allow($plugin, $id, $period) {
		
		$this->id = $id;
		Aitsu_Registry :: get()->session->plugins->allow[get_class() . '.' . $plugin][$id] = time() + $period;
		
		/*
		 * Unset values no longer valid to reduce the amount of information
		 * in the session object.
		 */
		foreach (Aitsu_Registry :: get()->session->plugins->allow as $key => $plugins) {
			foreach ($plugins as $id => $time) {
				if ($time < time()) {
					unset(Aitsu_Registry :: get()->session->plugins->allow[$key][$id]);
				}
			} 
		}
	}

	public final function isAllowed($plugin, $id) {

		if (!isset (Aitsu_Registry :: get()->session->plugins->allow[get_class() . '.' . $plugin][$id])) {
			return false;
		}
		
		if (time() > Aitsu_Registry :: get()->session->plugins->allow[get_class() . '.' . $plugin][$id]) {
			return false;
		}

		return true;
	}
	
	public function getAction($action) {
		
		return Aitsu_Registry :: get()->config->adminRoot . 'admin/index.php/article/' . get_class($this) . '/do/' . $action . '/id/' .  $this->id . '/';
	}
	
	public function setId($id) {
		
		$this->id = $id;
	}

	public static function register() {

		return (object) array (
			'name' => get_class(self :: _getInstance()),
			'tabname' => get_class(self :: _getInstance()),
			'enabled' => false
		);
	}

}