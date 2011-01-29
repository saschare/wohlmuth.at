<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Meta_PagetitleAsHeadline_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$instance = new self();

		$output = '';
		if ($instance->_get('PagetitleAsHeadline', $output)) {
			return $output;
		}

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$headline = htmlentities(Aitsu_Content_Text :: get('Headline', 0), ENT_COMPAT, 'UTF-8');
		if (empty ($headline)) {
			$headline = htmlentities(stripslashes(Aitsu_Core_Article :: factory()->pagetitle), ENT_COMPAT, 'UTF-8');
		}

		if ($params->tag == 'no') {
			$output = $headline;
		} else {
			$output = '<' . $params->tag . '>' . $headline . '</' . $params->tag . '>';
		}

		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 60 * 60 * 24 * 30);

		return $output;
	}

}