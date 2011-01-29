<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_Content_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		$index = empty ($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$startTag = isset ($params->tag) ? '<' . $params->tag . '>' : '';
		$endTag = isset ($params->tag) ? '</' . $params->tag . '>' : '';

		$return = '';
		if (Aitsu_Registry :: isEdit()) {
			$return = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $return;
		}

		if (Aitsu_Registry :: isEdit()) {
			$return .= '<div style="padding-top:5px; padding-bottom:5px;">';
		} else {
			$return .= '<div>';
		}

		if ($params->type == 'HTML') {
			return $return . $startTag . Aitsu_Content_Html :: get($params->index) . $endTag . '</div>';
		}

		if ($params->type == 'Text') {
			if (isset ($params->words)) {
				return $return . $startTag . Aitsu_Content_Text :: get($params->index, $params->words) . $endTag . '</div>';
			} else {
				return $return . $startTag . Aitsu_Content_Text :: get($params->index) . $endTag . '</div>';
			}
		}

	}
}