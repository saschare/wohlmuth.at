<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Rewrite_Abstract implements Aitsu_Rewrite_Interface {

	public function register() {

		return false;
	}

	public function rewriteOutput($html) {

		return $html;
	}
}