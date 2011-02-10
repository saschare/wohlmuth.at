<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class SyndicationArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4d4ae108-e148-4276-a9b5-0abf7f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		try {
			if (Aitsu_Db :: fetchOne('' .
				'select count(*) from _syndication_source ' .
				'where idclient = :idclient', array (
					':idclient' => Aitsu_Registry :: get()->session->currentClient
				))) {
				$enabled = true;
			}
		} catch (Exception $e) {
			$enabled = false;
		}

		return (object) array (
			'name' => 'syndication',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Syndication'),
			'enabled' => self :: getPosition($idart, 'syndication') && $enabled,
			'position' => self :: getPosition($idart, 'syndication'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$this->view->idart = $this->getRequest()->getParam('idart');
	}

	public function addAction() {

		$sourceIdartlang = $this->getRequest()->getParam('idartlang');
		$sourceId = $this->getRequest()->getParam('sourceid');
		$name = $this->getRequest()->getParam('name');

		$idart = $this->getRequest()->getParam('idart');
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;
		$idartlang = Aitsu_Db :: fetchOne('' .
		'select idartlang from _art_lang ' .
		'where ' .
		'	idart = :idart ' .
		'	and idlang = :idlang', array (
			':idart' => $idart,
			':idlang' => $idlang
		));

		$resource = Aitsu_Persistence_SyndicationResource :: factory(array (
			$sourceId,
			$sourceIdartlang
		))->addIdartlang($idartlang);
		
		/*
		 * Set the resource's name.
		 */
		$name = substr(preg_replace('|/{2}|', '', $name), -255);
		$resource->setResourceName($name);
		
		/*
		 * Initially populate the resource with data or update the data.
		 */
		$resource->load(1);

		$this->_helper->json((object) array (
			'success' => true
		));
	}

	public function storeAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;
		
		$resources = Aitsu_Persistence_SyndicationResource :: getResources($idart, $idlang);

		$data = array ();
		if ($resources) {
			foreach ($resources as $resource) {
				$data[] = (object) $resource;
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}
}