<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

class Module_HTML_Content_Static_Class extends Aitsu_Module_Tree_Abstract {
	
	protected function _init() {
		
		$view = $this->_getView();
		
		$template = Aitsu_Content_Config_Radio :: set($this->_index, 'HtmlStaticTemplate', '', $this->_getTemplates(), 'Template');
		
		if (empty($template)) {
			$template = 'index';
		}
		
		$startTag = '';
		$endTag = '';
		if (Aitsu_Registry :: isEdit()) {
			$startTag = '<div>';
			$endTag = '</div>';
		}

		$output = preg_replace('/<\\!-{2}.*?\\-{2}>\\s*/s', '', $view->render($template . '.phtml'));
		
		return $startTag . $output . $endTag;
	}
}