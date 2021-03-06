<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_Syndication_Proxy_Class extends Aitsu_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('Syndication.Proxy', true);

		$domain = $this->_params->domain;
		$base = $this->_params->base;
		$url = urlencode(isset ($_REQUEST['purl']) ? $_REQUEST['purl'] : $params->start);
		$expression = isset ($params->xpath) ? $params->xpath : "//body";

		$output = '';
		if ($this->_get('Proxy' . md5($domain . $base . $url), $output)) {
			return $output;
		}

		$client = new Zend_Http_Client('http://' . $domain . $base . $url, array (
			'maxredirects' => 5,
			'timeout' => 10
		));

		if (isset ($this->_params->user) && isset ($this->_params->password)) {
			$client->setAuth($this->_params->user, $this->_params->password);
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
		$output = $this->_rewriteContent($output, $domain, $base);

		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $this->_context['params'] . '</code>' . $output;
		}

		$this->_save($output, 60 * 60 * 24);

		return $output;
	}

	protected function _rewriteContent($content, $domain, $base) {

		$content = preg_replace('@((?:http://|https://)?' . $domain . $base . '(.*?))"@', ":self:$2\"", $content);
		$content = preg_replace('@' . $base . '(.*?)"@', ":self:$1\"", $content);

		$content = str_replace(':self:', '{ref:idart-' . Aitsu_Registry :: get()->env->idart . '}?purl=', $content);

		return $content;
	}
}