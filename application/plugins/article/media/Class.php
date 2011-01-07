<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class MediaArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cab3816-66e8-483a-b133-14a27f000101';

	protected $_idartlang;

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();

		$idart = $this->getRequest()->getParam('idart');

		if ($idart == null) {
			return;
		}

		$this->_idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang from _art_lang ' .
		'where idart = :idart and idlang = :idlang', array (
			':idart' => $idart,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		$this->view->idartlang = $this->_idartlang;
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'media',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Media'),
			'enabled' => self :: getPosition($idart, 'media'),
			'position' => self :: getPosition($idart, 'media'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->idart = $this->getRequest()->getParam('idart');
	}

	public function storeAction() {

		$files = Aitsu_Core_File :: getFiles($this->_idartlang, '*', 'filename', true, true);

		$data = array ();
		if ($files) {
			foreach ($files as $file) {
				$data[] = (object) $file;
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

	public function uploadAction() {

		Aitsu_Core_File :: upload($this->getRequest()->getParam('idart'), $_FILES['file']['name'], $_FILES['file']['tmp_name']);

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');
		$id = $this->getRequest()->getParam('mediaid');

		Aitsu_Core_File :: delete($idart, $id);

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function saveAction() {

		try {
			$file = Aitsu_Core_File :: factory($this->getRequest()->getParam('idartlang'), $this->getRequest()->getParam('mediaid'));
			$file->filename = $this->getRequest()->getParam('filename');
			$file->medianame = $this->getRequest()->getParam('name');
			$file->subline = $this->getRequest()->getParam('subline');
			$file->description = $this->getRequest()->getParam('description');
			$file->save();

			$this->_helper->json(array (
				'success' => true
			));
		} catch (Exception $e) {
			$this->_helper->json(array (
				'success' => false,
				'status' => 'exception',
				'message' => $e->getMessage()
			));
		}
	}
}