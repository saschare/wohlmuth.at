<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */
 
/*
 * The blog plugin is in development state and should not yet be
 * used in production.
 */

class BlogCategoryController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4dd4ec0b-6e40-454a-a3ab-0adb7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register($idcat) {
		
		$pos = self :: getPosition($idcat, 'blog', 'category');

		return (object) array (
			'name' => 'blog',
			'tabname' => Aitsu_Translate :: translate('Blog'),
			'enabled' => $pos,
			'position' => $pos,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$cat = Aitsu_Persistence_Category :: factory($idcat)->load();

		$this->view->usePublishing = isset (Aitsu_Registry :: get()->config->sys->usePublishing) && Aitsu_Registry :: get()->config->sys->usePublishing == true;
		$this->view->idcat = $idcat;
		$this->view->categoryname = $cat->name;
		$this->view->articles = Aitsu_Persistence_View_Articles :: full($idcat, null);
		$this->view->isInFavories = Aitsu_Persistence_CatFavorite :: factory($idcat)->load()->isInFavorites();
		$this->view->isClipboardEmpty = !isset (Aitsu_Registry :: get()->session->clipboard->articles) || count(Aitsu_Registry :: get()->session->clipboard->articles) == 0;
	}

	public function articlesAction() {

		$data = array ();

		$arts = Aitsu_Persistence_View_Articles :: full($this->getRequest()->getParam('idcat'), null);
		if ($arts) {
			foreach ($arts as $art) {
				$data[] = (object) array (
					'id' => $art['idart'],
					'title' => $art['title'],
					'pagetitle' => $art['pagetitle'],
					'urlname' => $art['urlname'],
					'online' => $art['online'],
					'published' => $art['published'],
					'isstart' => $art['isstart']
				);
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}
}