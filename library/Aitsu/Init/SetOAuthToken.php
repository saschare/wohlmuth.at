<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Aitsu_Init_SetOAuthToken implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (substr($_SERVER['REQUEST_URI'], 0, strlen('/oauth2callback')) != '/oauth2callback') {
			return;
		}
		
		header('Location: ' . $_GET['state'] . '?code=' . $_GET['code']);
		// header('Location: ' . $_GET['state']);
		exit(0);
	}
}