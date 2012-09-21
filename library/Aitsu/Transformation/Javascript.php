<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 *
 * {@id $Id: Javascript.php 18373 2010-08-27 09:50:04Z akm $}
 */

class Aitsu_Transformation_Javascript implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}
		
		$js = Aitsu_Util_Javascript :: getReferences();
		if (!empty($js)) {
			$refs = '';
			foreach ($js as $ref) {
				$refs .= '<script src="' . $ref . '" type="text/javascript"></script>' . "\n";
			}
			$event->bootstrap->pageContent = str_replace('</body>', "{$refs}</body>", $event->bootstrap->pageContent);
		}

		$js = Aitsu_Util_Javascript :: get();
		if (!empty ($js)) {
			$js = '<script type="text/javascript">' . $js . '</script>';
			$event->bootstrap->pageContent = str_replace('</body>', "{$js}</body>", $event->bootstrap->pageContent);
		}
	}

}