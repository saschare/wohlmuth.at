<?php


/**
 * WebSocketServer Class
 * based on http://code.google.com/p/phpwebsocket/
 * @author DerFichtl AT gmail.com / @DerFichtl on Twitter
 * 
 * @author (of slight modifications) Andreas Kummer, w3concepts AG
 * 
 * {@id $Id: Server.php 18818 2010-09-17 10:10:47Z akm $}
 */

abstract class Aitsu_Websocket_Abstract_Server {

	protected $address = null;
	protected $port = null;
	protected $users = array ();
	protected $master = null;
	protected $sockets = array ();
	protected $maxConnection = 99;

	public function __construct($address, $port) {

		$this->address = $address;
		$this->port = $port;

		$this->connectMaster($address, $port);
	}

	public function getUsers() {
		
		return $this->users;
	}

	protected function connectMaster() {
		
		$this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("socket_create() failed");
		$this->sockets[] = $this->master;	
		socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die("socket_option() failed");
		socket_bind($this->master, $this->address, $this->port) or die("socket_bind() failed");
		socket_listen($this->master, 20) or die("socket_listen() failed");
		
		return $this->master;
	}

	public function run() {
		
		$buffer = '';
		
		while (true) {
			$changed = $this->sockets;
			
			socket_select($changed, $write = NULL, $except = NULL, NULL);
			
			foreach ($changed as $socket) {
				if ($socket == $this->master) {
					$client = socket_accept($this->master);
					if ($client < 0) {
						console("socket_accept() failed");
						continue;
					} else {
						$this->connect($client);
					}
				} else {
					$bytes = @ socket_recv($socket, $buffer, 2048, 0);
					if ($bytes == 0) {
						$this->disconnect($socket);
					} else {
						$user = $this->getUserBySocket($socket);
						if (!$user->handshake) {
							$user->doHandshake($buffer);
						} else {
							$user->lastAction = time();
							$this->_process($user, $buffer);
						}
					}
				}
			}
		}

	}
	
	abstract protected function _process($user, $msg);

	public function connect($socket) {
		
		$this->users[] = new WebSocketUser($socket);
		$this->sockets[] = $socket;
	}

	public function disconnect($socket) {
		
		if ($this->users) {
			$found = null;
			$n = count($this->users);
			for ($i = 0; $i < $n; $i++) {
				if ($this->users[$i]->socket == $socket) {
					$found = $i;
					break;
				}
			}
			if (!is_null($found)) {
				array_splice($this->users, $found, 1);
			}
			$index = array_search($socket, $this->sockets);
			socket_close($socket);
			if ($index >= 0) {
				array_splice($this->sockets, $index, 1);
			}
		}
	}

	public function getUserBySocket($socket) {
		
		$found = null;
		
		foreach ($this->users as $user) {
			if ($user->socket == $socket) {
				$found = $user;
				break;
			}
		}
		
		return $found;
	}

	protected function _send($client, $msg) {
		
		@ socket_write($client, $msg, strlen($msg));
	}
	
}