<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: SqlException.php 19481 2010-10-21 15:15:07Z akm $}
 */

class Aitsu_Event_Listener_SqlException implements Aitsu_Event_Listener_Interface {
	
	public static function notify(Aitsu_Event_Abstract $event) {
		
		$message = 'SQL Exception occured:';
		$message .= "\nMessage: " . $event->exception->getMessage();
		$message .= "\nTrace:\n" . $event->exception->getTraceAsString();
		$message .= "\n Query: " . $event->query;
		$message .= "\n Params: " . print_r($event->params, true);
		
		trigger_error($message);
	}
}