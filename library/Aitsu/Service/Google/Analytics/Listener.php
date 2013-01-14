<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2013, w3concepts AG
 */

class Aitsu_Service_Google_Analytics_Listener implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}
		
		if (isset($_GET['anonymous'])) {
			return;
		}
		
		$js = Aitsu_Service_Google_Analytics :: getScript();
		if (!empty ($js)) {
			$event->bootstrap->pageContent = str_replace('</head>', "\n{$js}\n</head>", $event->bootstrap->pageContent);
		}
	}

}