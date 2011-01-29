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

		$this->view->idart = $this->getRequest()->getParam('idart');
	}

	public function addAction() {

		$idart = $this->getRequest()->getParam('idart');
		$token = $this->getRequest()->getParam('token');
		$value = $this->getRequest()->getParam('value');

		if (!empty($token)) {
			Aitsu_Persistence_Article :: factory($idart)->addTag($token, $value);
		}

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function storeAction() {

		$idart = $this->getRequest()->getParam('idart');
		$tags = Aitsu_Persistence_Article :: factory($idart)->getTags();

		$data = array ();
		foreach ($tags as $tag) {
			$data[] = (object) $tag;
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');
		$tagid = $this->getRequest()->getParam('tagid');

		Aitsu_Persistence_Article :: factory($idart)->removeTag($tagid);

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	/**
	 * @since 2.1.0 - 12.01.2011
	 */
	public function tagstoreAction() {

		$filter = array (
			(object) array (
				'clause' => 'tag like',
				'value' => '%' . $this->getRequest()->getParam('query') . '%'
			)
		);

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Tag :: getStore(100, 0, $filter)
		));
	}
}