<?php


/**
 * Text content.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Text.php 16999 2010-06-10 14:18:04Z akm $}
 */

class Aitsu_Content_Text {
	
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
	
	public static function get($token, $words = 5) {

		return Aitsu_Content :: get($token, Aitsu_Content :: PLAINTEXT, null, null, $words);	
	}
}