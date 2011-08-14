<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Meta_PageTitle_Class extends Aitsu_Module_Abstract {

	protected function _init() {

		Aitsu_Content_Edit :: noEdit('HTML.Meta.PageTitle', true);
		
		$prefix = isset($this->_params->prefix) ? $this->_params->prefix : ''; 
		$suffix = isset($this->_params->suffix) ? $this->_params->suffix : ''; 

		$output = '';
		if ($this->_get('PageTitle', $output)) {
			return $output;
		}

		$pageTitle = Aitsu_Core_Article :: factory()->pagetitle;

		$output =  '<title>' . $prefix . $pageTitle . $suffix . '</title>';
		
		if (Aitsu_Registry :: isEdit()) {
			$output = '<code class="aitsu_params" style="display:none;">' . $this->_context['params'] . '</code>' . $output;
		}

		$this->_save($output, 'eternal');

		return $output;
	}
}