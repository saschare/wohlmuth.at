<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Interface.php 18066 2010-08-12 09:40:21Z akm $}
 */

interface Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event);

}