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

		$idart = $this->getRequest()->getParam('idart');

		$this->view->crosslinks = Aitsu_Persistence_Article :: factory($idart)->load()->crosslinks;
	}

	public function articlesbytermAction() {

		$return = array ();

		$term = $this->getRequest()->getParam('term');

		$arts = Aitsu_Persistence_Article :: getByTerm($term, 20);
		if ($arts) {
			foreach ($arts as $art) {
				$art = (object) $art;
				$return[] = (object) array (
					'id' => $art->idartlang,
					'label' => $art->title . ' (' . $art->idart . ' [' . $art->idartlang . '])',
					'desc' => $art->pagetitle . ' / ' . $art->category,
					'value' => $art->idartlang
				);
			}
		}

		$this->_helper->json($return);
	}

	public function addAction() {

		$idart = (int) $this->getRequest()->getParam('idart');
		$idartlangA = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;
		$idartlangB = preg_replace('/[^\\d]*/', '', $this->getRequest()->getParam('id'));
		
		if ($this->getRequest()->getParam('linkidart') != null) {
			$idartlangB = Aitsu_Persistence_Article :: factory(preg_replace('/[^\\d]*/', '', $this->getRequest()->getParam('linkidart')))->load()->idartlang;
		}

		Aitsu_Persistence_Article :: addCrosslink($idartlangA, $idartlangB);

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => Aitsu_Translate :: translate('Link added.'),
			'list' => $this->view->partial('crosslinks.phtml', array (
				'crosslinks' => Aitsu_Persistence_Article :: factory($idart)->load(true)->crosslinks
			))
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idartlang = Aitsu_Persistence_Article :: factory($idart)->load()->idartlang;
		$ids = $this->getRequest()->getParam('ids');
		$ids = explode(',', $ids);

		foreach ($ids as $linkid) {
			$linkid = str_replace('crosslink-', '', $linkid);			
			Aitsu_Persistence_Article :: removeCrosslink($idartlang, $linkid);
		}

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => Aitsu_Translate :: translate('Link removed.'),
			'list' => $this->view->partial('crosslinks.phtml', array (
				'crosslinks' => Aitsu_Persistence_Article :: factory($idart)->load(true)->crosslinks
			))
		));
	}
}