<?php


/**
 * Single folder source.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Folder.php 15745 2010-03-31 22:30:42Z akm $}
 */

require_once ('Aitsu/Core/Source/File/List.php');

class Aitsu_Core_Source_File_Folder extends Aitsu_Core_Source_File_List {

	protected $pattern;
	protected $idlang;

	protected function __construct($path, $pattern) {

		$this->idlang = Aitsu_Registry :: get()->env->idlang;

		$this->path = $path;
		$this->pattern = $pattern;
	}

	public static function getInstance($path, $pattern = '%') {

		static $instance;

		if (substr($path, -1, 1) != '%' && substr($path, -1, 1) != '/') {
			$path = $path . '/';
		}

		if (!isset ($instance)) {
			$instance = new self($path, $pattern);
		}

		$instance->path = $path;
		$instance->pattern = $pattern;

		return $instance;
	}

	public function fetch($orderBy = 'filename', $ascending = true, $offset = 0, $limit = null) {

		$this->files = array ();

		$sqlLimit = ($limit != NULL) ? "LIMIT {$offset}, {$limit} " : "";
		$asc = $ascending ? 'asc' : 'desc';

		$results = Aitsu_Db :: fetchAll("" .
		"select " .
		"	upload.dirname as dirname, " .
		"	upload.filename as filename, " .
		"	metadata.medianame as medianame, " .
		"	metadata.description as description, " .
		"	upload.filetype as filetype, " .
		"	upload.size as size " .
		"from _upl as upload " .
		"left join _upl_meta as metadata on upload.idupl = metadata.idupl and metadata.idlang = ? " .
		"where " .
		"	upload.dirname like ? " .
		"	and upload.filename like ? " .
		"	and upload.filename not like ? " .
		"order by {$orderBy} {$asc} " .
		"$sqlLimit" .
		"", array (
			$this->idlang,
			addcslashes(str_replace('%%', '%', "{$this->path}"), '\_'),
			addcslashes(str_replace('%%', '%', "{$this->pattern}"), '\_'),
			addcslashes("__%", "\_")
		));
		
		$this->numberOfFiles = count($results);

		$returnValue = array ();
		for ($i = 0; $i < count($results); $i++) {
			$returnValue[$i]['filename'] = $results[$i]['filename'];
			$returnValue[$i]['path'] = $results[$i]['dirname'] . $results[$i]['filename'];
			$returnValue[$i]['medianame'] = ($results[$i]['medianame'] == '') ? $results[$i]['filename'] : urldecode($results[$i]['medianame']);
			$returnValue[$i]['description'] = $results[$i]['description'];
			$returnValue[$i]['type'] = $results[$i]['filetype'];
			$returnValue[$i]['size'] = $results[$i]['size'];
		}

		$this->files = $returnValue;

		return $this;
	}

}