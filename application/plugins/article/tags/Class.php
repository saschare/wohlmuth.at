<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class TagsArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cb71a90-bae4-4626-aee9-21487f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'tags',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Tags'),
			'enabled' => self :: getPosition($idart, 'tags'),
			'position' => self :: getPosition($idart, 'tags'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idart = $this->getRequest()->getParam('idart');

		$this->view->tags = Aitsu_Persistence_Article :: factory($idart)->getTags();
	}

	public function addAction() {

		$idart = $this->getRequest()->getParam('idart');
		$token = $this->getRequest()->getParam('token');
		$value = $this->getRequest()->getParam('value');

		Aitsu_Persistence_Article :: factory($idart)->addTag($token, $value);

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => sprintf(Aitsu_Translate :: translate('Tag %s added.'), $token),
			'list' => $this->view->partial('taglist.phtml', array (
				'tags' => Aitsu_Persistence_Article :: factory($idart)->getTags()
			))
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');
		$tagids = $this->getRequest()->getParam('tagids');
		$tagids = explode(',', $tagids);

		foreach ($tagids as $tagid) {
			$tagid = str_replace('tagid-', '', $tagid);
			Aitsu_Persistence_Article :: factory($idart)->removeTag($tagid);
		}

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => Aitsu_Translate :: translate('Tag removed.'),
			'list' => $this->view->partial('taglist.phtml', array (
				'tags' => Aitsu_Persistence_Article :: factory($idart)->getTags()
			))
		));
	}

	public function tagAction() {

		$term = $this->getRequest()->getParam('term');

		$return = array ();

		$tags = Aitsu_Persistence_Tag :: getByName('%' . $term . '%', 20);
		if ($tags) {
			foreach ($tags as $tag) {
				$return[] = (object) array (
					'id' => $tag->tagid,
					'label' => $tag->tag,
					'value' => $tag->tag
				);
			}
		}

		$this->_helper->json($return);
	}
}