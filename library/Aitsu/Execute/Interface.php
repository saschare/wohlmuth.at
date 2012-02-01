<?php


/**
 * The execute interface flags a class as one that may
 * be accessed by the url domain.tld/lib/path/to/class.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2012, w3concepts AG
 */
interface Aitsu_Execute_Interface {

	public static function execute();
}