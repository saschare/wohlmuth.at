<?php


/**
 * Strip whitespace from source.
 *
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: StripWhiteSpace.php 18229 2010-08-20 06:58:18Z akm $}
 */
class Aitsu_Ee_Transformation_StripWhiteSpace implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!isset($event->bootstrap->pageContent)) {
			return;
		}
	
		// Remove white space at the very beginning of the document
		$event->bootstrap->pageContent = preg_replace('/^\\s*/', "", $event->bootstrap->pageContent);
		
		// Remove white space between tags
		$event->bootstrap->pageContent = preg_replace('/>\\s*</s', "><", $event->bootstrap->pageContent);
		
		// Remove consecutive white space
		$event->bootstrap->pageContent = preg_replace('/\\s{2,}/', " ", $event->bootstrap->pageContent);
	}

}