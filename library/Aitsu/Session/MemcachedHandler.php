<?php


/**
 * Memcached session handler.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id$}
 */

class Aitsu_Session_MemcachedHandler implements Zend_Session_SaveHandler_Interface {

	protected $name;
	protected $cache;

	public function open($save_path, $name) {

		$this->name = 'SESSION_' . $name . '_';

		$frontendOptions = array (
			'lifetime' => 60 * 60,
			'automatic_serialization' => false
		);

		$server = Aitsu_Registry :: get()->config->memcached->server->toArray();
		$backendOptions = array (
			'server' => $server
		);

		$this->cache = Zend_Cache :: factory('Output', 'Memcached', $frontendOptions, $backendOptions);
	}

	public function close() {
	}

	public function read($id) {

		return $this->cache->load($this->name . $id);
	}

	public function write($id, $data) {

		$this->cache->save($data, $this->name . $id);
	}

	public function destroy($id) {

		$this->cache->remove($this->name . $id);
	}

	public function gc($maxlifetime) {
	}

}