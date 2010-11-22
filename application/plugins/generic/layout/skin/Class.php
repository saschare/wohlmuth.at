<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19027 2010-09-28 06:26:58Z akm $}
 */

class SkinPluginController extends Aitsu_Adm_Plugin_Controller {

	protected $_dir;

	public function init() {

		$this->view->placeholder('left')->set($this->view->partial('links.phtml'));
	}

	public function indexAction() {

	}

	public function fscontentAction() {

		$return = array ();

		$id = $this->getRequest()->getParam('id');
		if (empty ($id)) {
			$dir = APPLICATION_PATH . '/skins';
		} else {
			$dir = strtok($id, '-');
			$dir = strtok("\n");
		}

		$files = scandir($dir);
		$this->_dir = $dir;
		usort($files, array (
			$this,
			'_compareDirsAndFiles'
		));
		foreach ($files as $file) {
			if (substr($file, 0, 1) != '.') {
				if (preg_match('/\\.(.{2,5})$/', $file, $match)) {
					if (in_array($match[1], array (
							'phtml',
							'css',
							'js'
						))) {
						$return[] = array (
							'data' => $file,
							'attr' => array (
								'id' => 'directory-' . $dir . '/' . $file,
								'class' => 'file file-' . $match[1]
							),
							'icon' => 'folder',
							'state' => ''
						);
					}
				}
				elseif (is_dir($dir . '/' . $file)) {
					$return[] = array (
						'data' => $file,
						'attr' => array (
							'id' => 'directory-' . $dir . '/' . $file,
							'class' => 'folder'
						),
						'icon' => 'folder',
						'state' => 'closed'
					);
				}
			}
		}

		$this->_helper->json($return);
	}

	public function loadAction() {

		$return = array ();

		$id = substr($this->getRequest()->getParam('id'), strlen('directory-' . APPLICATION_PATH . '/skins'));

		preg_match('/\\.(\\w{2,5})$/', $id, $match);
		$parsers = array (
			'css' => 'CSSParser',
			'js' => 'JSParser',
			'phtml' => 'HTMLMixedParser',
			'html' => 'HTMLMixedParser'
		);

		$return['content'] = file_get_contents(APPLICATION_PATH . '/skins' . $id);
		$return['parser'] = $parsers[$match[1]];
		$return['title'] = $id;
		$return['id'] = $id;
		$return['isWritable'] = is_writable(APPLICATION_PATH . '/skins' . $id) ? 1 : 0;

		$this->_helper->json($return);
	}
	
	public function saveAction() {
		
		$return = array();
		
		$id = $this->getRequest()->getParam('id');
		$content = $this->getRequest()->getParam('content');
		
		$path = APPLICATION_PATH . '/skins' . $id;
		
		if (is_writable($path)) {
			file_put_contents($path, $content);
			$return['status'] = Zend_Registry :: get('Zend_Translate')->translate('File saved') . ' [' . $id . ']';
		} else {
			$return['status'] = Zend_Registry :: get('Zend_Translate')->translate('File is not writable. Save cannot be done.');
		}
		
		$this->_helper->json($return);
	}

	protected function _compareDirsAndFiles($a, $b) {

		$aDir = is_dir($this->_dir . '/' . $a);
		$bDir = is_dir($this->_dir . '/' . $b);

		if ($aDir !== $bDir) {
			if ($aDir) {
				return -1;
			} else {
				return +1;
			}
		}

		if ($a == $b) {
			return 0;
		}

		return ($a < $b) ? -1 : 1;
	}
}