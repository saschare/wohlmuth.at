<?php


/**
 * PageTitle ShortCode.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 17813 2010-07-29 09:01:04Z akm $}
 */

class Aitsu_Ee_Module_PageTitle_Class extends Aitsu_Ee_Module_Abstract {

	public static function about() {

		return (object) array (
			'name' => 'Page title',
			'description' => Zend_Registry :: get('Zend_Translate')->translate('Returns the page title of the current article.'),
			'type' => 'Header',
			'author' => (object) array (
				'name' => 'Andreas Kummer',
				'copyright' => 'w3concepts AG'
			),
			'version' => '1.0.0',
			'status' => 'stable',
			'url' => null,
			'id' => '4ca0a1a5-18b8-45fe-8e37-206f7f000101'
		);
	}

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('PageTitle', true);
		
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