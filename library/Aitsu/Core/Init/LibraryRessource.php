<?php


/**
 * Library ressource.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: LibraryRessource.php 15728 2010-03-31 19:15:58Z akm $}
 */

require_once 'Aitsu/Core/Init/Interface.php';

class Aitsu_Core_Init_LibraryRessource implements Aitsu_Core_Init_Interface {

	public static function init() {

		$pos = strpos($_GET['libraryressource'], '/');
		$library = substr($_GET['libraryressource'], 0, $pos);
		$source = substr($_GET['libraryressource'], $pos);

		if (Aitsu_Registry :: get()->config->rewrite->modrewrite) {
			$url = '/libraryressource';
		} else {
			$url = 'front_content.php?libraryressource=';
		}

		Aitsu_ScriptLibrary_Include :: factory($library, $url)->out($source);
		exit(0);
	}
}