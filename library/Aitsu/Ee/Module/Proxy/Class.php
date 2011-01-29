<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_Proxy_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Proxy', true);

		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$domain = $params->domain;
		$base = $params->base;
		$url = urlencode(isset ($_REQUEST['purl']) ? $_REQUEST['purl'] : $params->start);
		$expression = isset ($params->xpath) ? $params->xpath : "//body";

		$instance = new self();

		$output = '';
		if ($instance->_get('Proxy' . md5($domain . $base . $url), $output)) {
			return $output;
		}

		$client = new Zend_Http_Client('http://' . $domain . $base . $url, array (
			'maxredirects' => 5,
			'timeout' => 10
		));

		if (isset ($params->user) && isset ($params->password)) {
			$client->setAuth($params->user, $params->password);
		}

		if (!empty ($_POST)) {
			$client->setParameterPost($_POST);
			$output = $client->request('POST')->getBody();
		} else {
			$output = $client->request()->getBody();
		}

		/*
		 * Isolate the tag using the specified xpath expression. If no expression
		 * is given, the body tag is taken.
		 */
		$output = Aitsu_Html_Filter_Dom :: factory($output)->byXPath($expression);

		/*
		 * Rewrite URLs starting with $domain followed by $base or $base only as 
		 * well as relative URLs.
		 * 
		 * Depending on the use more sophisticated rewrite rules might be necessary.
		 */
		$output = $instance->_rewriteContent($output, $domain, $base);

		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 60 * 60 * 24);

		return $output;
	}

	protected function _rewriteContent($content, $domain, $base) {

		$content = preg_replace('@((?:http://|https://)?' . $domain . $base . '(.*?))"@', ":self:$2\"", $content);
		$content = preg_replace('@' . $base . '(.*?)"@', ":self:$1\"", $content);

		$content = str_replace(':self:', '{ref:idart-' . Aitsu_Registry :: get()->env->idart . '}?purl=', $content);

		return $content;
	}
}