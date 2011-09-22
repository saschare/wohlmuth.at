<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class GooglelocatorArticleController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4e7b5a8c-7ac8-4f3e-8a7f-12057f000101';

	public function init() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();
	}

	public static function register($idart) {

		return (object) array (
			'name' => 'googlelocator',
			'tabname' => Aitsu_Registry :: get()->Zend_Translate->translate('Google Locator'),
			'enabled' => self :: getPosition($idart, 'googlelocator'),
			'position' => self :: getPosition($idart, 'googlelocator'),
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$id = $this->getRequest()->getParam('idart');

		$form = Aitsu_Forms :: factory('googlelocator', APPLICATION_PATH . '/plugins/article/googlelocator/forms/googlelocator.ini');
		$form->title = Aitsu_Translate :: translate('Google Locator');
		$form->url = $this->view->url(array (
			'plugin' => 'googlelocator',
			'paction' => 'index'
		), 'aplugin');

		$data = Aitsu_Db :: fetchRow('' .
		'select ' .
		'	gg.address, ' .
		'	gg.lat, ' .
		'	gg.lng ' .
		'from _art_lang artlang ' .
		'left join _art_geolocation geoloc on artlang.idartlang = geoloc.idartlang ' .
		'left join _google_geolocation gg on geoloc.idlocation = gg.id ' .
		'where ' .
		'	artlang.idart = :idart ' .
		'	and artlang.idlang = :idlang', array (
			':idart' => $id,
			':idlang' => Aitsu_Registry :: get()->session->currentLanguage
		));

		if ($data) {
			$form->setValues(array_merge($data, array (
				'idart' => $id
			)));
		} else {
			$form->setValues(array (
				'idart' => $id
			));
		}

		if ($this->getRequest()->getParam('loader')) {
			$this->view->form = $form;
			header("Content-type: text/javascript");
			return;
		}

		try {
			if ($form->isValid()) {
				Aitsu_Event :: raise('backend.article.edit.save.start', array (
					'idart' => $id
				));

				$values = $form->getValues();
				$coord = Aitsu_Service_Google_Geocode :: getInstance()->locate($values['address']);

				if ($coord->id != null) {
					/*
					 * Persist the data.
					 */
					$idartlang = Aitsu_Db :: fetchOne('' .
					'select idartlang from _art_lang ' .
					'where ' .
					'	idart = :idart ' .
					'	and idlang = :idlang', array (
						':idart' => $id,
						':idlang' => Aitsu_Registry :: get()->session->currentLanguage
					));
					Aitsu_Db :: query('' .
					'delete from _art_geolocation where idartlang = :idartlang', array (
						':idartlang' => $idartlang
					));
					Aitsu_Db :: query('' .
					'insert into _art_geolocation ' .
					'(idartlang, idlocation) ' .
					'values ' .
					'(:idartlang, :idlocation)', array (
						':idartlang' => $idartlang,
						':idlocation' => $coord->id
					));
				}

				$this->_helper->json((object) array (
					'success' => true,
					'data' => $coord
				));
			} else {
				$this->_helper->json((object) array (
					'success' => false,
					'errors' => $form->getErrors()
				));
			}
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'exception' => true,
				'message' => $e->getMessage()
			));
		}
	}

}