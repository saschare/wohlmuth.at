<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class TodoArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4d26f1cd-7df0-4ddf-a5c6-12617f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'todo',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Todo'),
			'enabled' => true,
			'position' => 0,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idartlang = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;

		$this->view->idart = $idart;
		$this->view->idartlang = $idartlang;
	}

	public function storeAction() {

		$filter = Aitsu_Util_ExtJs :: encodeFilters($this->getRequest()->getParam('filter'));

		$filter[] = (object) array (
			'clause' => 'idartlang =',
			'value' => $this->getRequest()->getParam('idartlang')
		);

		$this->_helper->json((object) array (
			'data' => Aitsu_Persistence_Todo :: getStore(100, 0, $filter)
		));
	}

	public function saveAction() {

		$todo = Aitsu_Persistence_Todo :: factory($this->getRequest()->getParam('todoid'))->load();
		$todo->title = $this->getRequest()->getParam('title');
		$todo->description = $this->getRequest()->getParam('description');
		$todo->duedate = $this->getRequest()->getParam('duedate');
		$todo->idartlang = $this->getRequest()->getParam('idartlang');
                $todo->userid = $this->getRequest()->getParam('userid');
		$todo->save();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function deleteAction() {

		$todo = Aitsu_Persistence_Todo :: factory($this->getRequest()->getParam('todoid'))->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function statusAction() {

		$todo = Aitsu_Persistence_Todo :: factory($this->getRequest()->getParam('todoid'))->load();
		$todo->status = $this->getRequest()->getParam('status');
		$todo->save();

		$this->_helper->json((object) array (
			'success' => true
		));
	}
}