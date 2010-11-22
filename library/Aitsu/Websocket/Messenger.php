<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Messenger.php 18821 2010-09-17 16:36:17Z akm $}
 */

class Aitsu_Websocket_Messenger extends Aitsu_Websocket_Abstract_Server {

	public static function getInstance($address, $port = '8080') {

		static $instance = array ();
		
		if (!isset ($instance[$address . ':' . $port])) {
			$instance[$address . ':' . $port] = new self($address, $port);
		}

		return $instance[$address . ':' . $port];
	}

	protected function _process($user, $msg) {

		$return = 'test';

		foreach ($this->getUsers() as $user) {
			$server->send($user->socket, json_encode($return));
		}
	}

	public function inform($msg) {

		$return = 'test';

		foreach ($this->getUsers() as $user) {
			$server->send($user->socket, json_encode($return));
		}
	}
}