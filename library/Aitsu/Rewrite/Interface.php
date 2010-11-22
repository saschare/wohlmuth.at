<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Interface.php 19262 2010-10-12 13:32:25Z akm $}
 */

interface Aitsu_Rewrite_Interface {

	public static function getInstance();

	public function registerParams();

	public function rewriteOutput($html);
}