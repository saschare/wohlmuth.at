<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2013, w3concepts AG
 */
class Aitsu_Service_Google_Analytics {

	protected $_data = array ();
	protected $_pageView = true;

	private function __construct() {
	}

	public static function getInstance() {

		static $instance;

		if (!isset ($instance)) {
			$instance = new self();
		}

		$instance->_data['_setAccount'] = Aitsu_Config :: get('google.analytics.account');

		$domain = isset ($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
		$domain = empty ($domain) && isset ($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $domain;
		$instance->_data['_setDomainName'] = empty ($domain) ? 'domain.tld' : $domain;

		$instance->_data['_setAllowLinker'] = Aitsu_Config :: get('google.analytics.allowlinker') ? 'true' : 'false';

		return $instance;
	}

	public function getPush() {

		$return = '';

		array_walk($this->_data, array (
			$this,
			'_escapeForJs'
		));

		foreach ($this->_data as $key => $val) {
			$return .= "\t_gaq.push(['$key', '$val']);\n";
		}

		if ($this->_pageView) {
			$return .= "\t_gaq.push(['_trackPageview']);\n";
		}

		return $return;
	}

	public function getJQueryBinding() {

		if (!Aitsu_Config :: get('google.analytics.jquerybinding')) {
			return '';
		}

		if (!isset ($this->_data['_setAllowLinker']) || !$this->_data['_setAllowLinker']) {
			return '';
		}

		$return = "<script type=\"text/javascript\">\n";
		$return .= "\t" . '$(document).ready(function() {' . "\n";
		$return .= "\t\t" . '$("a[href*=\'' . $this->_data['_setDomainName'] . '\']").click(function() {' . "\n";
		$return .= "\t\t\t_gaq.push(['_link', this.href]);\n";
		$return .= "\t\t\treturn false;\n\t\t});\n";
		$return .= "\t\t" . '$("form[action*=\'' . $this->_data['_setDomainName'] . '\']").attr("onSubmit","_gaq.push([\'_linkByPost\', this])");' . "\n";
		$return .= "\t});\n</script>\n";

		return $return;
	}

	public static function getScript() {

		if (empty (Aitsu_Service_Google_Analytics :: getInstance()->_data['_setAccount'])) {
			return '';
		}

		$return = Aitsu_Service_Google_Analytics :: getInstance()->getJQueryBinding();
		$return .= '<script type="text/javascript">' . "\n";
		$return .= "\tvar _gaq = _gaq || [];\n";
		$return .= Aitsu_Service_Google_Analytics :: getInstance()->getPush();
		$return .= Aitsu_Service_Google_Analytics_Event :: getPush();
		$return .= Aitsu_Service_Google_Analytics_Transaction :: getPush();
		$return .= "\t(function() {\n\t\tvar ga = document.createElement('script');\n\t\tga.type = 'text/javascript';\n\t\tga.async = true;\n\t\tga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n\t\tvar s = document.getElementsByTagName('script')[0];\n\t\ts.parentNode.insertBefore(ga, s);\n\t})();\n";
		$return .= '</script>' . "\n";

		return $return;
	}

	protected function _escapeForJs(& $val) {

		$val = preg_replace("/\r?\n/", "\\n", addslashes($val));
	}

	public static function noPageView() {

		Aitsu_Service_Google_Analytics :: getInstance()->_pageView = false;
	}

}