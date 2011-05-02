<?php


/**
 * Inclusion class for script library ressources.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Include.php 15729 2010-03-31 19:16:37Z akm $}
 */
 
class Aitsu_ScriptLibrary_Include {
	
	private $library;
	private $url;
	
	private function __construct($library, $url) {
		
		$this->library = $library;
		$this->url = $url;
	}
	
	public function factory($library, $url = null) {
		
		static $instance = array();
		
		if (!isset($instance[$library])) {
			$instance[$library] = new self($library, $url);
		}
		
		return $instance[$library];
	}
	
	public function out($source) {
		
		if (substr($source, 0, strlen('/Images')) == '/Images') {
			$size = getimagesize(dirname(__FILE__) . "/{$this->library}{$source}");
			header("Content-type: {$size['mime']}");
			readfile(dirname(__FILE__) . "/{$this->library}{$source}");
			return;
		}
		
		if (substr($source, -4) == '.css') {
			header('Content-type: text/css');
		} elseif (substr($source, -3) == '.js') {
			header('Content-type: application/javascript');
		} elseif (substr($source, -4) == '.xml') {
			header('Content-type: text/xml');
		} else {
			header('Content-type: text/plain');
		}
		
		echo str_replace('?url?', $this->url . '/' . $this->library, file_get_contents(dirname(__FILE__) . "/{$this->library}{$source}"));
	}
}