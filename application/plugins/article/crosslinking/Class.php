<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class CrosslinkingArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cbd68e4-6b4c-487c-9fd7-13237f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'crosslinking',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Crosslinking'),
			'enabled' => self :: getPosition($idart, 'crosslinking'),
			'position' => self :: getPosition($idart, 'crosslinking'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->idart = $this->getRequest()->getParam('idart');
	}

	public function storeAction() {

		$idart = $this->getRequest()->getParam('idart');
		$links = Aitsu_Persistence_Article :: factory($idart)->load()->crosslinks;

		$data = array ();
		if ($links) {
			foreach ($links as $link) {
				$data[] = (object) $link;
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

	public function addAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idartlangA = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;
		$idartlangB = $this->getRequest()->getParam('idartlang');

		Aitsu_Persistence_Article :: addCrosslink($idartlangA, $idartlangB);

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idartlangB = $this->getRequest()->getParam('idartlang');
		$idartlangA = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;

		Aitsu_Persistence_Article :: removeCrosslink($idartlangA, $idartlangB);

		$this->_helper->json((object) array (
			'success' => true
		));
	}
}