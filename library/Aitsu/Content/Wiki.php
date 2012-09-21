<?php


/**
 * Wiki markup content.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Wiki.php 16378 2010-05-12 10:04:41Z akm $}
 */

class Aitsu_Content_Wiki {
	
	protected $idartlang = null;
	protected $token = null;
	protected $content = null;
	
	protected function __construct($token) {
		
		$this->idartlang = Aitsu_Registry :: get()->env->idartlang;
		$this->token = $token;
	}
	
	protected function factory($token) {
		
		static $instance = array();
		
		if (!isset($instance[$token])) {
			$instance[$token] = new self($token);
		}
		
		return $instance[$token];
	}
	
	public static function get($token) {

		return Aitsu_Parser_Wiki :: parse(Aitsu_Content :: get($token, Aitsu_Content :: WIKICODE, null, null, 5));	
	}
}