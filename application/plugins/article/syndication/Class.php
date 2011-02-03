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

}