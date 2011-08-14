<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

include_once (APPLICATION_PATH . '/modules/Schema/Org/CreativeWork/Class.php');

class Module_Schema_Org_Blog_Class extends Module_Schema_Org_CreativeWork_Class {

	protected function _init() {
	}

	protected function _main() {

		$view = $this->_getView();

		return $view->render('index.phtml');
	}

	protected function _getView() {

		$view = parent :: _getView();
		
		$blogPosts = Aitsu_Content_Config_Textarea :: set($this->_index, 'schema.org.Blog.BlogPosts', 'Blog posts', 'Blog');
		if (!empty($blogPosts)) {
			$view->blogPosts = Aitsu_Module_SchemaOrg_Container :: factory($this->_index, 'BlogPosting', 'Blog', $blogPosts);
		}

		$view->index = $this->_index;

		return $view;
	}
}