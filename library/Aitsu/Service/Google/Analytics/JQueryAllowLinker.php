<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2013, w3concepts AG
 */

class Aitsu_Service_Google_Analytics_JQueryAllowLinker implements Aitsu_Event_Listener_Interface {

	public static function notify(Aitsu_Event_Abstract $event) {

		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}
		
		if (isset($_GET['anonymous'])) {
			return;
		}
		
		$ga = Aitsu_Config :: get('google.analytics.account');
		if (empty($ga)) {
			return;
		}
		
		$view = new Zend_View();
		$view->setScriptPath(dirname(__FILE__) . '/JQueryAllowLinker');
		
		$domain = isset ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		$view->domain = empty ($domain) && isset ($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $domain;
		
		$js = $view->render('js.phtml');
		
		if (!empty ($js)) {
			$event->bootstrap->pageContent = str_replace('</body>', "\n{$js}\n</body>", $event->bootstrap->pageContent);
		}
	}

}