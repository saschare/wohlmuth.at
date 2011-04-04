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

		header("Content-type: text/javascript");

		$setIdlang = $this->getRequest()->getParam('setidlang');
		if (!empty ($setIdlang)) {
			Aitsu_Registry :: get()->session->currentLanguage = $setIdlang;
			Aitsu_Registry :: get()->session->currentClient = Aitsu_Db :: fetchOne('' .
			'select idclient from _lang where idlang = :idlang', array (
				':idlang' => $setIdlang
			));
		}

		$this->view->articles = Aitsu_Persistence_Lastopened :: factory(1)->load()->get(100);
		$this->view->favorites = Aitsu_Persistence_CatFavorite :: getAll();

		$this->_loadCategoryPlugins(0);
	}

	/**
	 * @since 2.1.0.0 - 31.12.2010
	 */
	public function ropstoreAction() {

		$data = array ();

		foreach (Aitsu_Persistence_Lastopened :: factory(1)->load()->get(100) as $rop) {
			$data[] = (object) array (
				'id' => $rop['idart'],
				'idcat' => $rop['idcat'],
				'name' => $rop['title'],
				'category' => $rop['name']
			);
		}

		$this->_helper->json((object) array (
			'data' => $data
		));
	}

	/**
	 * Action to deliver extjs tree panels with the
	 * necessary async tree information.
	 */
	public function treesourceAction() {
		$syncLang = 0;
		$return = array ();

		$id = $this->getRequest()->getParam('node');
		if (empty ($id)) {
			$id = 0;
		}

		$categories = Aitsu_Persistence_View_Category :: cat($id, Aitsu_Registry :: get()->session->currentLanguage, null);
		if ($categories) {
			foreach ($categories as $cat) {
				if ($cat['public'] == 1) {
					if ($cat['online'] == 1) {
						$cls = 'treecat-online';
					} else {
						$cls = 'treecat-offline';
					}
				} else {
					$cls = 'treecat-private';
				}
				$return[] = array (
					'id' => $cat['idcat'],
					'idcat' => $cat['idcat'],
					'text' => $cat['name'],
					'leaf' => false,
					'iconCls' => $cls,
					'online' => $cat['online'] == 1,
					'public' => $cat['public'] == 1,
					'type' => 'category'
				);
			}
		}

		$articles = Aitsu_Persistence_View_Articles :: art($id, Aitsu_Registry :: get()->session->currentLanguage, $syncLang);
		if ($articles) {
			$artCounter = 0;
			if (empty ($_REQUEST['showpages'])) {
				$maxPages = Aitsu_Config :: get('backend.pagetree.maxpages');
				$maxPages = empty ($maxPages) ? 100 : $maxPages;
			} else {
				if ($_REQUEST['showpages'] == 'all') {
					$maxPages = PHP_INT_MAX;
				} else {
					$maxPages = (int) $_REQUEST['showpages'];
				}
			}
			foreach ($articles as $art) {
				$artCounter++;
				if ($artCounter <= $maxPages) {
					if ($art['isstart']) {
						if ($art['online'] == 1) {
							$cls = 'treepage-index-online';
						} else {
							$cls = 'treepage-index-offline';
						}
					} else {
						if ($art['online'] == 1) {
							$cls = 'treepage-online';
						} else {
							$cls = 'treepage-offline';
						}
					}
					$return[] = array (
						'id' => 'idart-' . $art['idart'],
						'idart' => $art['idart'],
						'idartlang' => $art['idartlang'],
						'text' => $art['title'],
						'leaf' => true,
						'iconCls' => $cls,
						'online' => $art['online'] == 1,
						'type' => 'page',
						'indexpage' => $art['isstart'] == 1
					);
				}
			}
		}

		$this->_helper->json($return);
	}

	public function editAction() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();

		$idart = $this->getRequest()->getParam('id');
		$this->view->idart = $idart;
		Aitsu_Persistence_Lastopened :: factory($idart)->save();

		$this->view->art = Aitsu_Persistence_Article :: factory($idart)->load();

		$plugins = array ();
		foreach (Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/plugins/article', 'Class.php') as $plugin) {
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
				$plugins[] = $registry;
			}
		}

		uasort($plugins, array (
			$this,
			'_comparePosition'
		));
		$plugins = array_reverse($plugins);

                $this->view->hidePublishing = (Aitsu_Config::get('sys.usePublishing') ? false : true);

		foreach ($plugins as $plugin) {
			$this->_helper->actionStack('article', 'plugin', 'default', array (
				'plugin' => $plugin->name,
				'idart' => $idart
			));
		}
	}

	/**
	 * @since 2.1.0.0 - 23.12.2010
	 */
	public function categoryoverviewAction() {

		header("Content-type: text/javascript");
		$this->_helper->layout->disableLayout();

		$id = $this->getRequest()->getParam('id');

		$this->view->cat = Aitsu_Persistence_Category :: factory($id)->getData();

		$configSets = array (
			array (
				0,
				'[inherit]'
			)
		);
		$cSets = Aitsu_Persistence_ConfigSet :: getByName();
		foreach ($cSets as $set) {
			$configSets[] = array (
				$set->configsetid,
				$set->identifier
			);
		}
		$this->view->configSets = $configSets;

		$this->_loadCategoryPlugins($id);
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

	public function newarticleAction() {

		$id = $this->getRequest()->getParam('idcat');

		$art = Aitsu_Persistence_Article :: factory();
		$art->title = Aitsu_Translate :: translate('Article') . ' ' . date('Y-m-d H:i:s');
		$art->idclient = Aitsu_Registry :: get()->session->currentClient;
		$art->idcat = $id;
		$art->save();

		$this->_helper->json($art->getData());
	}

	public function toggleonlineAction() {

		$id = $this->getRequest()->getParam('idart');

		$art = Aitsu_Persistence_Article :: factory($id)->load();
		if ($art->online == 1) {
			$art->online = 0;
		} else {
			$art->online = 1;
		}
		$art->save();

		$this->_helper->json((object) array (
			'idart' => $id,
			'online' => $art->online
		));
	}

	public function startpublishingAction() {

		$id = $this->getRequest()->getParam('idart');

		$art = Aitsu_Persistence_Article :: factory($id)->publish()->load(true);

		$this->_helper->json((object) array (
			'idart' => $art->idart,
			'published' => $art->ispublished
		));
	}

	public function deleteAction() {

		$idart = $this->getRequest()->getParam('idart');

		try {
			Aitsu_Persistence_Article :: factory($idart)->remove();
			$this->_helper->json((object) array (
				'success' => true,
				'message' => Aitsu_Translate :: translate('Article deleted') . ' [ID ' . $idart . ']'
			));
		} catch (Exception $e) {
			$this->_helper->json(array (
				'success' => false,
				'message' => $e->getMessage()
			));
		}
	}

	public function makeindexAction() {

		$idart = $this->getRequest()->getParam('idart');

		try {
			Aitsu_Persistence_Article :: factory($idart)->setAsIndex();
			$this->_helper->json((object) array (
				'success' => true,
				'idart' => $idart,
				'status' => 'success',
				'message' => ''
			));
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
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

		$idart = $this->getRequest()->getParam('idart');
		$syncLang = $this->getRequest()->getParam('synclang');

		try {
			Aitsu_Persistence_Article :: factory($idart)->sync($syncLang);
			$this->_helper->json(array (
				'success' => true
			));
		} catch (Exception $e) {
			$this->_helper->json((object) array (
				'success' => false,
				'status' => 'exception',
				'message' => $e->getMessage(),
				'stacktrace' => $e->getTraceAsString()
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

	public function movepageAction() {

		$idart = $this->getRequest()->getParam('idart');
		$idcat = $this->getRequest()->getParam('idcat');
trigger_error(var_export(array($idart, $idcat), true));
		$art = Aitsu_Persistence_Article :: factory($idart)->moveTo($idcat)->load(true);

		$this->_helper->json((object) array (
			'success' => true,
			'data' => $art->getData()
		));
	}
}