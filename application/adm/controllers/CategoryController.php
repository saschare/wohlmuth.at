<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CategoryController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'client' => Aitsu_Registry :: get()->session->currentClient,
				'language' => Aitsu_Registry :: get()->session->currentLanguage,
				'area' => 'category'
			))) {
			throw new Exception('Access denied');
		}
	}

	public function indexAction() {

		$langs = Aitsu_Persistence_Language :: getByClient(Aitsu_Registry :: get()->session->currentClient);

		$accordion = $this->view->partial('category/accordion.phtml', array (
			'langs' => $langs
		));
		$this->view->placeholder('left')->set($accordion);

		$this->view->langs = $langs;
	}

	public function treecontentAction() {

		$return = array ();

		$id = $this->getRequest()->getParam('id');
		if (empty ($id)) {
			$id = 0;
		} else {
			$tmp = explode('-', $id);
			$id = $tmp[1];
		}
		$syncLang = $this->getRequest()->getParam('sync');

		$categories = Aitsu_Persistence_View_Category :: cat($id, Aitsu_Registry :: get()->session->currentLanguage, $syncLang);
		if ($categories) {
			foreach ($categories as $cat) {
				$classes = array ();
				if (isset ($cat['unsynced']) && $cat['unsynced'] == 1) {
					$classes[] = 'unsynced';
				} else {
					$classes[] = $cat['online'] == 1 ? 'online' : 'offline';
					$classes[] = $cat['public'] == 1 ? 'public' : 'locked';
				}
				$return[] = array (
					'data' => $cat['name'],
					'attr' => array (
						'id' => 'cat-' . $cat['idcat'],
						'class' => implode(' ', $classes)
					),
					'icon' => 'folder',
					'state' => $cat['haschildren'] == 1 ? 'closed' : ''
				);
			}
		}

		$this->_helper->json($return);
	}

	public function addnewAction() {

		try {
			$id = $this->getRequest()->getParam('idcat');
			$idcat = Aitsu_Persistence_Category :: factory($id)->insert(Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'sucess' => false,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json((object) array (
			'success' => true,
			'status' => 'success',
			'data' => Aitsu_Persistence_Category :: factory($idcat)->getData(),
			'parent' => $id
		));
	}

	public function deleteAction() {

		try {
			$id = $this->getRequest()->getParam('id');
			if (empty ($id)) {
				$id = 0;
			} else {
				$tmp = explode('-', $id);
				$id = $tmp[1];
			}

			Aitsu_Persistence_Category :: factory($id)->remove(Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'idcat' => $id
		));
	}

	public function setonlineAction() {

		try {
			$id = $this->getRequest()->getParam('idcat');
			$status = $this->getRequest()->getParam('status');
			$propagate = $this->getRequest()->getParam('propagate');

			Aitsu_Persistence_Category :: factory($id)->setOnline($status, $propagate, Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'idcat' => $id,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json((object) array (
			'idcat' => $id,
			'success' => true,
			'status' => 'success',
			'idcat' => $id
		));
	}

	public function setlockedAction() {

		try {
			$tmp = explode('-', $this->getRequest()->getParam('id'));
			$id = $tmp[1];
			$status = $this->getRequest()->getParam('status');
			$propagate = $this->getRequest()->getParam('propagate');

			Aitsu_Persistence_Category :: factory($id)->setPublic($status, $propagate, Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'idcat' => $id
		));
	}

	public function getcatdataAction() {

		try {
			$tmp = explode('-', $this->getRequest()->getParam('id'));
			$id = $tmp[1];

			$data = Aitsu_Persistence_Category :: factory($id)->getData();
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'data' => $data
		));
	}

	public function saveAction() {

		try {
			$tmp = explode('-', $this->getRequest()->getParam('idcat'));
			$idcat = $tmp[1];

			Aitsu_Persistence_Category :: factory($idcat)->load()->setValues(array (
				'name' => $this->getRequest()->getParam('name'),
				'urlname' => $this->getRequest()->getParam('urlname'),
				'configsetid' => $this->getRequest()->getParam('configset'),
				'config' => $this->getRequest()->getParam('config')
			))->save();
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'idcat' => $idcat
		));
	}

	public function updateAction() {

		$id = $this->getRequest()->getParam('idcat');
		$property = $this->getRequest()->getParam('property');
		$value = $this->getRequest()->getParam('value');

		if (in_array($value, array (
				'true',
				'false'
			))) {
			$value = $value == 'true' ? 1 : 0;
		}

		try {
			$data = Aitsu_Persistence_Category :: factory($id)->load()->setValues(array (
				$property => $value
			))->save()->getData();
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'sucess' => false,
				'status' => 'exception',
				'message' => $e->getMessage()
			));
			return;
		}

		$this->_helper->json((object) array (
			'sucess' => true,
			'data' => $data
		));
	}

	public function movecatAction() {

		try {
			$tmp = explode('-', $this->getRequest()->getParam('id'));
			$idcat = $tmp[1];

			$tmp = explode('-', $this->getRequest()->getParam('relative'));
			$neighbour = $tmp[1];

			$type = $this->getRequest()->getParam('type');

			$tmp = explode('-', $this->getRequest()->getParam('parent'));
			$parent = $tmp[1];

			$cat = Aitsu_Persistence_Category :: factory($idcat);

			if ($type == 'inside') {
				$cat->moveInsideCat($parent);
			}
			elseif ($type == 'before') {
				$cat->moveBeforeCat($neighbour);
			}
			elseif ($type == 'after') {
				$cat->moveAfterCat($neighbour);
			}
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'idcat' => $idcat,
			'neighbour' => $neighbour,
			'type' => $type,
			'parent' => $parent
		));
	}

	public function syncAction() {

		try {
			$tmp = explode('-', $this->getRequest()->getParam('id'));
			$idcat = $tmp[1];
			$syncLang = $this->getRequest()->getParam('syncLang');

			Aitsu_Persistence_Category :: factory($idcat)->synchronize($syncLang);
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json(array (
			'status' => 'success',
			'idcat' => $idcat
		));
	}
}