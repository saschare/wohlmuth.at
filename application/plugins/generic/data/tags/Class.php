<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19027 2010-09-28 06:26:58Z akm $}
 */

class TagsPluginController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

	}

	public function indexAction() {

		$this->view->tags = Aitsu_Persistence_Tag :: getByName('%', 500);
	}

	public function deleteAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$tags = $this->getRequest()->getParam('tags');
		$tags = explode(',', $tags);
		
		foreach ($tags as $tag) {
			$tagid = str_replace('tagid-', '', $tag);
			Aitsu_Persistence_Tag :: factory($tagid)->remove();
		}

		echo $this->view->partial('taglist.phtml', array (
			'tags' => Aitsu_Persistence_Tag :: getByName('%', 500)
		));
	}
}