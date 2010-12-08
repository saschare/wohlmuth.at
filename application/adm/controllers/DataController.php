<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

class DataController extends Zend_Controller_Action {

	public function init() {

		if (!Aitsu_Adm_User :: getInstance()->isAllowed(array (
				'area' => 'article'
			))) {
			throw new Exception('Access denied');
		}
		
		if ($this->getRequest()->getParam('ajax')) {
			$this->_helper->layout->disableLayout();
		}
	}

	public function indexAction() {

		$langs = Aitsu_Persistence_Language :: getByClient(Aitsu_Registry :: get()->session->currentClient);

		$datasetSelector = $this->view->partial('data/datasetSelector.phtml', array (
			'langs' => $langs,
			'articles' => Aitsu_Persistence_Lastopened :: factory(1)->load()->get()
		));
		$this->view->placeholder('left')->set($datasetSelector);

		/*
		 * Add plugins' head to the head and plugins' js at the bottom
		 * of the current page.
		 */
		$plugins = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/article', 'Class.php');
		$this->view->plugins = array ();
		$this->view->addScriptPath(APPLICATION_PATH . '/plugins/article');
		foreach ($plugins as $plugin) {
			$parts = preg_split('@[\\/]@', $plugin);
			$pluginName = $parts[count($parts) - 2];
			include_once ($plugin);
			$controller = ucfirst($pluginName) . 'Article';
			$controllerClass = $controller . 'Controller';
			$registry = call_user_func(array (
				$controllerClass,
				'register'
			), null);
			if ($registry->enabled) {
				if (is_readable(APPLICATION_PATH . '/plugins/article/' . $pluginName . '/views/meta.phtml')) {
					$this->view->partial($pluginName . '/views/meta.phtml');
				}
				$this->view->plugins[] = $registry;
			}
		}

		uasort($this->view->plugins, array (
			$this,
			'_comparePosition'
		));

		$this->view->articles = Aitsu_Persistence_Lastopened :: factory(1)->load()->get(100);
	}

	public function lastopenedAction() {

		$this->_helper->layout->disableLayout();

		$this->view->articles = Aitsu_Persistence_Lastopened :: factory(1)->load()->get();
	}

	public function favoritesAction() {

		$this->_helper->layout->disableLayout();

		$this->view->favorites = Aitsu_Persistence_CatFavorite :: getAll();
	}

	public function treecontentAction() {

		$return = array ();

		$id = $this->getRequest()->getParam('id');
		if (empty ($id)) {
			$id = 0;
		} else {
			preg_match('/\\d*$/', $id, $id);
			$id = $id[0];
		}
		$syncLang = $this->getRequest()->getParam('sync');

		$idprefix = $this->getRequest()->getParam('idprefix');

		$categories = Aitsu_Persistence_View_Category :: cat($id, Aitsu_Registry :: get()->session->currentLanguage, $syncLang);
		if ($categories) {
			foreach ($categories as $cat) {
				$classes = array ();
				$classes[] = $cat['online'] == 1 ? 'online' : 'offline';
				$classes[] = $cat['public'] == 1 ? 'public' : 'locked';
				$return[] = array (
					'data' => $cat['name'] . ' [' . $cat['idcat'] . ']',
					'attr' => array (
						'id' => $idprefix . 'cat-' . $cat['idcat'],
						'class' => implode(' ', $classes)
					),
					'icon' => 'folder',
					'state' => 'closed'
				);
			}
		}

		$articles = Aitsu_Persistence_View_Articles :: art($id, Aitsu_Registry :: get()->session->currentLanguage, $syncLang);
		if ($articles) {
			foreach ($articles as $art) {
				$classes = array ();
				$classes[] = $art['online'] == 1 ? 'online' : 'offline';
				$classes[] = $art['isstart'] == 1 ? 'start' : 'normal';
				$classes[] = 'article';
				$return[] = array (
					'data' => $art['title'],
					'attr' => array (
						'id' => $idprefix . 'idart-' . $art['idart'],
						'class' => implode(' ', $classes)
					),
					'icon' => 'article',
					'state' => ''
				);
			}
		}

		$this->_helper->json($return);
	}

	public function editAction() {

		$this->_helper->layout->disableLayout();

		$idart = substr($this->getRequest()->getParam('id'), strlen('idart-'));
		$this->view->idart = $idart;
		Aitsu_Persistence_Lastopened :: factory($idart)->save();

		$plugins = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/article', 'Class.php');
		$this->view->plugins = array ();
		foreach ($plugins as $plugin) {
			$parts = explode('/', $plugin);
			$pluginName = $parts[count($parts) - 2];
			include_once ($plugin);
			$controller = ucfirst($pluginName) . 'Article';
			$controllerClass = $controller . 'Controller';
			$registry = call_user_func(array (
				$controllerClass,
				'register'
			), $idart);
			if ($registry->enabled) {
				$this->view->plugins[] = $registry;
			}
		}

		uasort($this->view->plugins, array (
			$this,
			'_comparePosition'
		));
	}

	public function categoryoverviewAction() {

		$this->_helper->layout->disableLayout();

		$id = substr($this->getRequest()->getParam('id'), strlen('cat-'));

		$this->_loadCategoryPlugins($id);

		/*$syncLang = $this->getRequest()->getParam('sync');
		$cat = Aitsu_Persistence_Category :: factory($id)->load();

		$this->view->idcat = $id;
		$this->view->categoryname = $cat->name;
		$this->view->articles = Aitsu_Persistence_View_Articles :: full($id, $syncLang);
		$this->view->isInFavories = Aitsu_Persistence_CatFavorite :: factory($id)->load()->isInFavorites();
		$this->view->isClipboardEmpty = !isset (Aitsu_Registry :: get()->session->clipboard->articles) || count(Aitsu_Registry :: get()->session->clipboard->articles) == 0;*/
	}

	protected function _loadCategoryPlugins($id) {

		$plugins = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/category', 'Class.php');
		$this->view->plugins = array ();
		foreach ($plugins as $plugin) {
			$parts = explode('/', $plugin);
			$pluginName = $parts[count($parts) - 2];
			include_once ($plugin);
			$controller = ucfirst($pluginName) . 'Category';
			$controllerClass = $controller . 'Controller';
			$registry = call_user_func(array (
				$controllerClass,
				'register'
			), $id);
			if ($registry->enabled) {
				$this->view->plugins[] = $registry;
			}
		}

		uasort($this->view->plugins, array (
			$this,
			'_comparePosition'
		));
		
		$this->view->idcat = $id;
	}

	public function addtofavoritesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id');

		Aitsu_Persistence_CatFavorite :: factory($id)->save();

		$this->view->favorites = Aitsu_Persistence_CatFavorite :: getAll();
		$this->render('favorites');
	}

	public function removefromfavoritesAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = $this->getRequest()->getParam('id');

		Aitsu_Persistence_CatFavorite :: factory($id)->remove();

		$this->view->favorites = Aitsu_Persistence_CatFavorite :: getAll();
		$this->render('favorites');
	}

	public function newarticleAction() {

		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);

		$id = substr($this->getRequest()->getParam('id'), strlen('cat-'));

		$art = Aitsu_Persistence_Article :: factory();
		$art->title = Zend_Registry :: get('Zend_Translate')->translate('Article') . ' ' . date('Y-m-d H:i:s');
		$art->idclient = Aitsu_Registry :: get()->session->currentClient;
		$art->idcat = $id;
		$art->save();
		
		$this->_loadCategoryPlugins($id);

		$cat = Aitsu_Persistence_Category :: factory($id)->load();

		$this->view->categoryname = $cat->name;
		$this->view->articles = Aitsu_Persistence_View_Articles :: full($id);

		$this->render('categoryoverview');
	}

	public function toggleonlineAction() {

		$id = $this->getRequest()->getParam('id');

		$art = Aitsu_Persistence_Article :: factory($id)->load();
		if ($art->online == 1) {
			$art->online = 0;
		} else {
			$art->online = 1;
		}
		$art->save();

		$this->_helper->json(array (
			'online' => $art->online
		));
	}
	
	public function startpublishingAction() {
		
		$id = $this->getRequest()->getParam('id');
		
		$art = Aitsu_Persistence_Article :: factory($id)->publish()->load(true);
		
		$this->_helper->json(array (
			'published' => $art->ispublished
		));
	}

	public function deleteAction() {

		$id = $this->getRequest()->getParam('id');
		$idart = substr($id, strlen('idart-'));

		try {
			Aitsu_Persistence_Article :: factory($idart)->remove();
			$this->_helper->json(array (
				'status' => 'success',
				'message' => Zend_Translate :: translate('Article deleted') . ' [ID ' . $idart . ']'
			));
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => $e->getMessage()
			));
		}
	}

	public function makeindexAction() {

		$id = $this->getRequest()->getParam('id');
		$idart = substr($id, strlen('idart-'));

		try {
			Aitsu_Persistence_Article :: factory($idart)->setAsIndex();
			$this->_helper->json(array (
				'status' => 'success',
				'message' => ''
			));
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('An error occured while trying to set the specified article as index. ')
			));
		}
	}

	public function duplicateAction() {

		$id = $this->getRequest()->getParam('id');
		$idart = substr($id, strlen('idart-'));

		try {
			Aitsu_Persistence_Article :: factory($idart)->duplicate();
			$this->_helper->json(array (
				'status' => 'success',
				'message' => ''
			));
		} catch (Exception $e) {
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('An error occured while trying to duplicate the specified article. ')
			));
		}
	}

	public function syncAction() {

		$id = $this->getRequest()->getParam('id');
		$idart = substr($id, strlen('idart-'));
		$syncLang = $this->getRequest()->getParam('sync');

		try {
			Aitsu_Persistence_Article :: factory($idart)->sync($syncLang);
			$this->_helper->json(array (
				'status' => 'success',
				'message' => ''
			));
		} catch (Exception $e) {
			trigger_error('Exception occured. Thrown at ' . __FILE__ . ' on line ' . __LINE__);
			trigger_error($e->getTraceAsString());
			$this->_helper->json(array (
				'status' => 'exception',
				'message' => Zend_Registry :: get('Zend_Translate')->translate('An error occured while trying to synchronize the specified article. ')
			));
		}
	}

	protected function _comparePosition($a, $b) {

		if (isset ($a->position) && isset ($b->position)) {
			return $a->position < $b->position ? -1 : 1;
		}

		if (isset ($a->position)) {
			return -1;
		}

		if (isset ($b->position)) {
			return 1;
		}

		return strcmp($a->name, $b->name);
	}

	public function catpathAction() {

		$id = $this->getRequest()->getParam('id');
		$id = str_replace('idcat-', '', $id);

		$this->_helper->json(Aitsu_Persistence_Category :: path($id));
	}

	public function movehereAction() {

		$idcat = $this->getRequest()->getParam('idcat');
		foreach (Aitsu_Registry :: get()->session->clipboard->articles as $id) {
			Aitsu_Persistence_Article :: factory($id)->moveTo($idcat);
		}

		Aitsu_Registry :: get()->session->clipboard->articles = null;

		$this->_helper->json((object) array (
			'status' => 'success',
			'message' => Aitsu_Translate :: translate('Categories moved.')
		));
	}
}