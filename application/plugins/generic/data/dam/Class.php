<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class DamPluginController extends Aitsu_Adm_Plugin_Controller {

	public function init() {

	}

	public function indexAction() {

		$this->view->files = $this->_getFiles();
	}

	public function uploadAction() {

		$userId = Aitsu_Adm_User :: getInstance()->getId();

		$chunk = $this->getRequest()->getParam('chunk');
		$chunk = empty ($chunk) ? 0 : $chunk;
		$chunks = $this->getRequest()->getParam('chunks');
		$chunks = empty ($chunks) ? 0 : $chunks;
		$fileName = $this->getRequest()->getParam('name');

		if (!file_exists(APPLICATION_PATH . '/data/media/inbox/' . $userId)) {
			if (!file_exists(APPLICATION_PATH . '/data/media')) {
				mkdir(APPLICATION_PATH . '/data/media', 0777);
			}
			if (!file_exists(APPLICATION_PATH . '/data/media/inbox')) {
				mkdir(APPLICATION_PATH . '/data/media/inbox', 0777);
			}
			mkdir(APPLICATION_PATH . '/data/media/inbox/' . $userId, 0777);
		}
		$targetDir = APPLICATION_PATH . '/data/media/inbox/' . $userId;

		if (isset ($_SERVER["HTTP_CONTENT_TYPE"])) {
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		}
		elseif (isset ($_SERVER["CONTENT_TYPE"])) {
			$contentType = $_SERVER["CONTENT_TYPE"];
		}

		if (strpos($contentType, "multipart") !== false) {
			if (isset ($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				$out = fopen($targetDir . '/' . $fileName, $chunk == 0 ? "wb" : "ab");
				if ($out) {
					$in = fopen($_FILES['file']['tmp_name'], "rb");
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else {
						$this->_helper->json((object) array (
							'jsonrpc' => '2.0',
							'error' => (object) array (
								'code' => 101,
								'message' => Aitsu_Translate :: translate('Failed to open input stream.')
							),
							'id' => 'id'
						));
						return;
					}
					fclose($out);
					unlink($_FILES['file']['tmp_name']);
				} else {
					$this->_helper->json((object) array (
						'jsonrpc' => '2.0',
						'error' => (object) array (
							'code' => 102,
							'message' => Aitsu_Translate :: translate('Failed to open output stream.')
						),
						'id' => 'id'
					));
					return;
				}
			} else {
				$this->_helper->json((object) array (
					'jsonrpc' => '2.0',
					'error' => (object) array (
						'code' => 103,
						'message' => Aitsu_Translate :: translate('Failed to move uploaded file.')
					),
					'id' => 'id'
				));
				return;
			}
		} else {
			$out = fopen($targetDir . '/' . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				$in = fopen("php://input", "rb");
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else {
					$this->_helper->json((object) array (
						'jsonrpc' => '2.0',
						'error' => (object) array (
							'code' => 101,
							'message' => Aitsu_Translate :: translate('Failed to open input stream.')
						),
						'id' => 'id'
					));
				}
				fclose($out);
			} else {
				$this->_helper->json((object) array (
					'jsonrpc' => '2.0',
					'error' => (object) array (
						'code' => 102,
						'message' => Aitsu_Translate :: translate('Failed to open output stream.')
					),
					'id' => 'id'
				));
			}
		}

		$this->_helper->json((object) array (
			'jsonrpc' => '2.0',
			'result' => null,
			'id' => 'id'
		));
	}
	
	public function refreshinboxAction() {
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		$this->view->files = $this->_getFiles();
		echo $this->view->render('inbox/content.phtml');
	}

	protected function _getFiles() {

		$return = array ();

		$userId = Aitsu_Adm_User :: getInstance()->getId();

		$files = scandir(APPLICATION_PATH . '/data/media/inbox/' . $userId);
		if ($files) {
			foreach ($files as $file) {
				if ($file != '.' && $file != '..') {
					$path = APPLICATION_PATH . '/data/media/inbox/' . $userId . '/' . $file;
					$pathInfo = pathinfo($path);
					$return[] = (object) array (
						'basename' => $file,
						'path' => $path,
						'size' => filesize($path) / 1024,
						'timestamp' => filemtime($path),
						'extension' => $pathInfo['extension']
					);
				}
			}
		}
		
		return $return;
	}
}