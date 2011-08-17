<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
class Wdrei_Migros_Fitnesspark_Voucher_Pdf implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {
		
		if (!isset($_GET['url']) || substr($_GET['url'], 0, strlen('voucher/')) != 'voucher/') {
			return;
		}
		
		if (!preg_match('@^voucher/\\d*/\\d*/\\d{2,20}$@', $_GET['url'])) {
			return;
		}
		
		

		var_dump(explode('/', $_GET['url']));
		exit ();
	}
}