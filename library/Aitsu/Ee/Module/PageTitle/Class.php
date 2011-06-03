<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

/**
 * @deprecated 2.1.0 - 29.01.2011
 */
class Aitsu_Ee_Module_PageTitle_Class extends Aitsu_Ee_Module_Abstract {

	public static function init($context) {

		Aitsu_Content_Edit :: noEdit('PageTitle', true);

		$params = Aitsu_Util :: parseSimpleIni($context['params']);

		$prefix = isset ($params->prefix) ? $params->prefix : '';
		$suffix = isset ($params->suffix) ? $params->suffix : '';

		$instance = new self();

		$output = '';
		if ($instance->_get('PageTitle', $output)) {
			return $output;
		}

		$pageTitle = Aitsu_Core_Article :: factory()->pagetitle;

		$output = '<title>' . $prefix . $pageTitle . $suffix . '</title>';

		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' . $output;
		}

		$instance->_save($output, 'eternal');

		return $output;
	}
}