<?php


/**
 * Pagetitle as headline - shortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17855 2010-07-30 17:27:13Z akm $}
 */

class Aitsu_Ee_Module_PagetitleAsHeadline_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Page title as headline',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns either the specified string or - if empty - the page title of the current page.'),
			'type' => 'Content',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => 'a0725371-c565-11df-851a-0800200c9a66'
		);
	}

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