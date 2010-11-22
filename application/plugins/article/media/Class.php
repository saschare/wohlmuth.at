<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19916 2010-11-17 12:40:58Z akm $}
 */

class MediaArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cab3816-66e8-483a-b133-14a27f000101';

	protected $_idartlang;

	public function init() {

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

		$id = $this->getRequest()->getParam('idart');

		$this->view->pluginId = self :: ID;
		$this->view->files = Aitsu_Core_File :: getFiles($this->_idartlang, '*', 'filename', true, true);
	}

	public function uploadAction() {

		$this->_helper->viewRenderer->setNoRender(true);

		$idart = Aitsu_Db :: fetchOne('select idart from _art_lang where idartlang = :idartlang', array (
			':idartlang' => $this->getRequest()->getParam('idartlang')
		));

		$tmpFileName = $_FILES['Filedata']['tmp_name'];
		$fileName = $_FILES['Filedata']['name'];

		Aitsu_Core_File :: upload($idart, $fileName, $tmpFileName);

		echo '1';
	}

	public function filelistAction() {

		$id = $this->getRequest()->getParam('idart');

		$this->view->pluginId = self :: ID;
		$this->view->files = Aitsu_Core_File :: getFiles($this->_idartlang, '*', 'filename', true, true);
	}

	public function deleteAction() {

		$idartlang = $this->getRequest()->getParam('idartlang');
		$files = $this->getRequest()->getParam('delete');
		$files = str_replace('mediaid-', '', $files);

		Aitsu_Core_File :: delete($idartlang, explode(',', $files));

		$this->view->pluginId = self :: ID;
		$this->view->files = Aitsu_Core_File :: getFiles($idartlang, '*', 'filename', true, true);
	}

	public function editAction() {

		$idartlang = $this->getRequest()->getParam('idartlang');
		$mediaid = $this->getRequest()->getParam('mediaid');
		$mediaid = str_replace('mediaid-', '', $mediaid);

		$file = Aitsu_Core_File :: factory($idartlang, $mediaid);

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/media/forms/file.ini', 'edit'));
		$form->setAction($this->view->url());

		$form->setValues(array (
			'idartlang' => $idartlang,
			'mediaid' => $mediaid,
			'filename' => $file->filename,
			'medianame' => $file->medianame,
			'subline' => $file->subline,
			'description' => $file->description
		));

		$this->view->pluginId = self :: ID;
		$this->view->form = $form;
	}

	public function saveAction() {

		$form = new Aitsu_Form(new Zend_Config_Ini(APPLICATION_PATH . '/plugins/article/media/forms/file.ini', 'edit'));
		$form->setAction($this->view->url());

		if ($form->isValid($_POST)) {
			try {
				$file = Aitsu_Core_File :: factory($this->getRequest()->getParam('idartlang'), $this->getRequest()->getParam('mediaid'));
				$file->filename = $this->getRequest()->getParam('filename');
				$file->medianame = $this->getRequest()->getParam('medianame');
				$file->subline = $this->getRequest()->getParam('subline');
				$file->description = $this->getRequest()->getParam('description');
				$file->save();

				$this->_helper->json(array (
					'status' => 'success',
					'message' => Zend_Registry :: get('Zend_Translate')->translate('Meta data saved.')
				));
			} catch (Exception $e) {
				$this->_helper->json(array (
					'status' => 'exception',
					'message' => $e->getMessage()
				));
			}
		} else {
			$this->_helper->json(array (
				'status' => 'validationerror',
				'html' => (string) $form
			));
		}
	}
}