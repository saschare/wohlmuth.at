<?php


/**
 * Google Analytics.
 * 
 * @version 1.0.0 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Analytics.php 16535 2010-05-21 08:59:30Z akm $}
 */

class Aitsu_Service_Google_Analytics {

	protected $googleAnalyticsId;

	protected function __construct($googleAnalyticsId) {

		$this->googleAnalyticsId = $googleAnalyticsId;
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self(Aitsu_Registry :: get()->config->google->analytics->id);
		}

		return $instance;
	}

	public function getGa() {

		$js = '' .
		'<script type="text/javascript">' . "\n" .
		'	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");' . "\n" .
		'	document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));' . "\n" .
		'</script>' . "\n" .
		'' . "\n" .
		'<script type="text/javascript">' . "\n" .
		'	try{' . "\n" .
		'		var pageTracker = _gat._getTracker("' . $this->googleAnalyticsId . '");' . "\n" .
		'		pageTracker._trackPageview();' . "\n" .
		'	} catch(err) {}' . "\n" .
		'</script>';
		
		return $js;
	}
}