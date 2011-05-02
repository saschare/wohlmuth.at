<?php


/**
 * Add configured pre- or postfix to the title.
 * 
 * @version 1.0.0
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: AddToTitle.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Transformation_AddToTitle implements Aitsu_Transformation_Interface {

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	public function getContent($content) {
		
		$prefix = Aitsu_Registry :: get()->config->pagetitle->prefix;
		$suffix = Aitsu_Registry :: get()->config->pagetitle->suffix;
		
		return preg_replace('@<title>(.*?)</title>@', "<title>{$prefix}$1{$suffix}</title>", $content);
	}

}