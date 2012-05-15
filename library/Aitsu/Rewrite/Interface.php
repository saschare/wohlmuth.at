<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
interface Aitsu_Rewrite_Interface {

	public static function getInstance();

	public function registerParams();

	public function rewriteOutput($html);
}