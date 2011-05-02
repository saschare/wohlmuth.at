<?php


/**
 * File list. This class acts as a base class
 * for the file sources.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: List.php 15694 2010-03-31 13:47:11Z akm $}
 */

abstract class Aitsu_Core_Source_File_List implements Iterator {

	protected $path;    
	protected $files = array (); 
	protected $numberOfFiles = 0;
	protected $position = 0;

	abstract public static function getInstance($path);
	abstract public function fetch($limit = NULL);

	function getNumberOfFiles() {
		
		return $this->numberOfImages;
	}

	function rewind() {
		
		$this->position = 0;
	}

	function current() {
		
		return (object) $this->files[$this->position];
	}

	function key() {
		
		return $this->position;
	}

	function next() {
		
		++ $this->position;
		return (object) $this->files[$this->position - 1];
	}

	function valid() {
		
		return isset ($this->files[$this->position]);
	}
}