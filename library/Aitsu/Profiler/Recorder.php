<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Aitsu_Profiler_Recorder implements Aitsu_Event_Listener_Interface {
	
	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (isset($_GET['profile']) && $_GET['profile']) {
			/*
			 * Skip recording if profile mode is on.
			 */
			return;
		}
		
		Aitsu_Persistence_Profile :: factory()->save();
	}
}