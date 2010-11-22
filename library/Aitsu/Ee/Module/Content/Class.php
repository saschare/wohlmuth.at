<?php


/**
 * Content as ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17166 2010-06-17 08:56:31Z akm $}
 */

class Aitsu_Ee_Module_Content_Class extends Aitsu_Ee_Module_Abstract {
	
	public static function about() {

		return (object) array (
			'name' => 'Content',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Allows to input content in the edit area and outputs that content in the frontend.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725364-c565-11df-851a-0800200c9a66'
		);
	}

	public static function init($context) {

		$index = empty($context['index']) ? 'noindex' : $context['index'];
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$startTag = isset($params->tag) ? '<' . $params->tag . '>' : '';
		$endTag = isset($params->tag) ? '</' . $params->tag . '>' : '';
		
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
			if (isset($params->words)) {
				return $return . $startTag . Aitsu_Content_Text :: get($params->index, $params->words) . $endTag . '</div>';
			} else {
				return $return . $startTag . Aitsu_Content_Text :: get($params->index) . $endTag . '</div>';
			}
		}

	}
}