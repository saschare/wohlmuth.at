<?php


/**
 * REST interface.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Interface.php 17660 2010-07-21 13:06:15Z akm $}
 */

interface Aitsu_Api_Rest_Interface extends Aitsu_Api_Interface {
	
	public function restGet();
	
	public function restPut();
	
	public function restPost();
	
	public function restDelete();
	
	public function restHeader();
}