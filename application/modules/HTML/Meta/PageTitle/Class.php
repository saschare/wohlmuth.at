<?php


/**
 * PageTitle ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19945 2010-11-18 19:29:47Z akm $}
 */

class Module_HTML_PageTitle_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Page title',
			'description' => Aitsu_Translate :: translate('Returns the page title of the current article.'),
			'type' => 'Header',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ce57ea4-18ec-4583-923f-4c097f000101'
		);
	}

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.PageTitle', true);
		
		$params = Aitsu_Util :: parseSimpleIni($context['params']);
		
		$prefix = isset($params->prefix) ? $params->prefix : ''; 
		$suffix = isset($params->suffix) ? $params->suffix : ''; 

		$instance = new self();

		$output = '';
		if ($instance->_get('PageTitle', $output)) {
			return $output;
		}

		$pageTitle = Aitsu_Core_Article :: factory()->pagetitle;

		$output =  '<title>' . $prefix . $pageTitle . $suffix . '</title>';
		
		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 'eternal');

		return $output;
	}
}