<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class StandardCategoryController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd2cac0-f2a0-4ecc-98b1-0ace7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
	}

	public static function register($idcat) {

		return (object) array (
			'name' => 'standard',
			'tabname' => Aitsu_Translate :: translate('Overview'),
			'enabled' => true,
			'position' => 0,
			'id' => self :: ID
		);		
	}

	public function indexAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$syncLang = $this->getRequest()->getParam('sync');
		$cat = Aitsu_Persistence_Category :: factory($idcat)->load();

		$this->view->usePublishing = isset(Aitsu_Registry :: get()->config->sys->usePublishing) &&  Aitsu_Registry :: get()->config->sys->usePublishing == true;
		$this->view->idcat = $idcat;
		$this->view->categoryname = $cat->name;
		$this->view->articles = Aitsu_Persistence_View_Articles :: full($idcat, $syncLang);
		$this->view->isInFavories = Aitsu_Persistence_CatFavorite :: factory($idcat)->load()->isInFavorites();
		$this->view->isClipboardEmpty = !isset (Aitsu_Registry :: get()->session->clipboard->articles) || count(Aitsu_Registry :: get()->session->clipboard->articles) == 0;
	}
}