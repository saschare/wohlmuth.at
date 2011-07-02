<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class Module_HTML_Content_Static_Class extends Aitsu_Module_Abstract {
	
	public static function init($context) {
		
		$output = '';
		if ($instance->_get('HtmlStatic_' . $this->_index, $output)) {
			return $output;
		}
		
		$view = $instance->_getView();
		
		$template = Aitsu_Content_Config_Radio :: set($this->_index, 'HtmlStaticTemplate', '', $instance->_getTemplates(), 'Template');
		
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
		$this->_save($output, 'eternal');
		
		return $startTag . $output . $endTag;
	}
}