<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class StandardCategoryController extends Aitsu_Adm_Plugin_Controller {

	const ID = '4cd2cac0-f2a0-4ecc-98b1-0ace7f000101';

	public function init() {

		$this->_helper->layout->disableLayout();
		header("Content-type: text/javascript");
	}

	public static function register($idcat) {

		$pos = self :: getPosition($idcat, 'standard', 'category');

		return (object) array (
			'name' => 'standard',
			'tabname' => Aitsu_Translate :: translate('Overview'),
			'enabled' => $pos,
			'position' => $pos,
			'id' => self :: ID
		);
	}

	public function indexAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		$cat = Aitsu_Persistence_Category :: factory($idcat)->load();
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;

		$this->view->usePublishing = isset (Aitsu_Registry :: get()->config->sys->usePublishing) && Aitsu_Registry :: get()->config->sys->usePublishing == true;
		$this->view->idcat = $idcat;
		$this->view->categoryname = $cat->name;
		$this->view->isInFavories = Aitsu_Persistence_CatFavorite :: factory($idcat)->load()->isInFavorites();
		$this->view->isClipboardEmpty = !isset (Aitsu_Registry :: get()->session->clipboard->articles) || count(Aitsu_Registry :: get()->session->clipboard->articles) == 0;

		$this->view->allowEdit = Aitsu_Adm_User :: getInstance()->isAllowed(array (
			'language' => $idlang,
			'area' => 'article',
			'action' => 'update',
			'resource' => array (
				'type' => 'cat',
				'id' => $idcat
			)
		));

		$this->view->allowNew = Aitsu_Adm_User :: getInstance()->isAllowed(array (
			'language' => $idlang,
			'area' => 'article',
			'action' => 'insert',
			'resource' => array (
				'type' => 'cat',
				'id' => $idcat
			)
		));

		$this->view->hidePublishing = (Aitsu_Config :: get('sys.usePublishing') ? false : true);
	}

	public function articlesAction() {

		$user = Aitsu_Adm_User :: getInstance();
		$idcat = $this->getRequest()->getParam('idcat');
		$idlang = Aitsu_Registry :: get()->session->currentLanguage;

		if (isset ($_POST['xaction']) && $_POST['xaction'] == 'update') {
			$data = json_decode($_POST['data']);
			$idcat = Aitsu_Db :: fetchOne('' .
			'select idcat from _cat_art where idart = :idart', array (
				':idart' => $data->id
			));
			$arts = Aitsu_Db :: fetchAll('' .
			'select ' .
			'	artlang.idart, ' .
			'	artlang.idartlang ' .
			'from _art_lang artlang ' .
			'left join _cat_art catart on artlang.idart = catart.idart ' .
			'where ' .
			'	catart.idcat = :idcat ' .
			'	and artlang.idlang = :idlang ' .
			'order by ' .
			'	artlang.artsort asc', array (
				':idcat' => $idcat,
				':idlang' => $idlang
			));
			$pos = 0;
			for ($i = 0; $i < count($arts); $i++) {
				$idart = $arts[$i]['idart'];
				if ($pos == $data->artsort) {
					$pos++;
				}
				if ($idart == $data->id) {
					$artsort = $data->artsort;
				} else {
					$artsort = $pos++;
				}
				Aitsu_Db :: query('' .
				'update _art_lang ' .
				'set artsort = :artsort ' .
				'where ' .
				'	idartlang = :idartlang', array (
					':artsort' => $artsort,
					':idartlang' => $arts[$i]['idartlang']
				));
				Aitsu_Db :: query('' .
				'update _pub_art_lang ' .
				'set artsort = :artsort ' .
				'where ' .
				'	idartlang = :idartlang ' .
				'	and status = 1', array (
					':artsort' => $artsort,
					':idartlang' => $arts[$i]['idartlang']
				));
			}
		}

		$data = array ();

		$arts = Aitsu_Persistence_View_Articles :: full($idcat, null);
		if ($arts) {
			foreach ($arts as $art) {
				$data[] = (object) array (
					'id' => $art['idart'],
					'title' => $art['title'],
					'pagetitle' => $art['pagetitle'],
					'urlname' => $art['urlname'],
					'online' => $art['online'],
					'published' => $art['published'],
					'isstart' => $art['isstart'],
					'artsort' => $art['artsort']
				);
			}
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}
}