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

	/**
	 * Returns the path of the specified idcat using the
	 * idcats and beginning with the root level.
	 * @since 2.1.0.0 - 20.12.2010
	 */
	public function getpathAction() {

		$id = $this->getRequest()->getParam('idcat');
		$path = Aitsu_Persistence_Category :: path($id);

		$this->_helper->json((object) array (
			'path' => '/0/' . implode('/', $path)
		));
	}

	public function addtofavoritesAction() {

		$id = $this->getRequest()->getParam('idcat');
		Aitsu_Persistence_CatFavorite :: factory($id)->save();

		$this->_helper->json((object) array (
			'success' => true,
			'data' => Aitsu_Persistence_Category :: factory($id)->getData()
		));
	}

	public function removefavoriteAction() {

		$id = $this->getRequest()->getParam('idcat');
		Aitsu_Persistence_CatFavorite :: factory($id)->remove();

		$this->_helper->json((object) array (
			'success' => true
		));
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
			$id = $this->getRequest()->getParam('idcat');
			Aitsu_Persistence_Category :: factory($id)->remove(Aitsu_Registry :: get()->session->currentLanguage);
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json((object) array (
			'status' => 'success'
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

	/**
	 * Moves the selected category to the specified category before
	 * or after or at the end of the content of the parent category.
	 */
	public function movecatAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$parentid = $this->getRequest()->getParam('parentid');
		$next = $this->getRequest()->getParam('next');
		$previous = $this->getRequest()->getParam('previous');

		try {
			$cat = Aitsu_Persistence_Category :: factory($idcat);

			if (empty ($next) && empty ($previous)) {
				$cat->moveInsideCat($parentid);
			}
			elseif (!empty ($next)) {
				$cat->moveBeforeCat($next);
			}
			elseif (!empty ($previous)) {
				$cat->moveAfterCat($previous);
			}
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function syncAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$syncLang = $this->getRequest()->getParam('synclang');

		try {
			Aitsu_Persistence_Category :: factory($idcat)->synchronize($syncLang);
			$this->_helper->json(array (
				'success' => true
			));
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
			));
		}
	}
}