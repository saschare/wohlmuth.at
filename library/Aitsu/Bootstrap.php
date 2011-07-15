<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */

set_include_path(realpath(dirname(__FILE__) . '/..') . PATH_SEPARATOR . get_include_path());
set_include_path(realpath(dirname(__FILE__) . '/../..') . PATH_SEPARATOR . get_include_path());
require_once 'Zend/Loader/Autoloader' . '.php';

class Aitsu_Bootstrap {

	protected $configured = false;
	public $pageContent = null;
	protected $isEE = false;
	protected $debug = false;

	protected function _DisableMagicQuotes() {

		if (get_magic_quotes_gpc()) {
			$process = array (
				& $_GET,
				& $_POST,
				& $_COOKIE,
				& $_REQUEST
			);
			while (list ($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset ($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][stripslashes($k)] = $v;
						$process[] = & $process[$key][stripslashes($k)];
					} else {
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
			unset ($process);
		}
	}

	protected function _ReturnCache() {

		if (isset ($_GET['edit']) && $_GET['edit'] == 1) {
			/*
			 * Skip, as we are in edit mode.
			 */
			return;
		}

		if (isset ($_GET['clearcache']) || isset ($_GET['profile'])) {
			return;
		}

		$maxage = 10;

		$files = glob(CACHE_PATH . '/' . REQUEST_HASH . '.*.html');
		if ($files !== false) {
			foreach ($files as $file) {
				if (preg_match('/\\w{32}\\.([0-9]*).([0-9a-z]*)\\.html/', $file, $match)) {
					if (isset ($_SERVER['HTTP_IF_NONE_MATCH']) && $match[2] == $_SERVER['HTTP_IF_NONE_MATCH']) {
						header("Pragma: public");
						// header("Cache-Control: max-age=" . $maxage);
						header("ETag: {$match[2]}");
						header("HTTP/1.1 304 Not Modified");
						header("Connection: Close");
						exit (0);
					}
					elseif ($match[1] > time()) {
						header("Pragma: public");
						// header("Cache-Control: max-age=" . $maxage);
						header("ETag: {$match[2]}");
						readfile($file);
						exit (0);
					}
				}
			}
		}
	}

	protected function _RegisterAutoloader() {

		$autoloader = Zend_Loader_Autoloader :: getInstance();
		$libPath = realpath(APPLICATION_PATH . '/../library');
		$libs = scandir($libPath);
		foreach ($libs as $lib) {
			if (!in_array($lib, array (
					'.',
					'..',
					'Zend'
				)) && is_dir($libPath . '/' . $lib)) {
				$autoloader->registerNamespace($lib . '_');
			}
		}
	}

	protected function _SetErrorHandler() {

		set_error_handler(array (
			'Aitsu_Core_Logger',
			'errorHandler'
		), E_ALL /* ^ E_NOTICE */
		);
	}

	protected function _ReadConfiguration() {

		if (substr($_SERVER['REQUEST_URI'], 1, 5) == 'admin' || substr($_SERVER['REQUEST_URI'], 1, 10) == 'skin/admin') {
			Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('admin');
			$this->configured = true;
			return;
		}

		if (isset ($_GET['edit']) || isset ($_GET['preview'])) {
			/*
			 * The client is determined by investigation of the 
			 * parameter id, which represents the idartlang of the 
			 * page to be edited.
			 */
			Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('config');
			$config = Aitsu_Db :: fetchOne('' .
			'select ' .
			'	client.config ' .
			'from _art_lang as artlang ' .
			'left join _lang as lang on artlang.idlang = lang.idlang ' .
			'left join _clients as client on lang.idclient = client.idclient ' .
			'where artlang.idartlang = :idartlang', array (
				':idartlang' => $_GET['id']
			));
			if (empty ($config)) {
				$config = 'default';
			}
			Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('clients/' . $config);

			if (isset ($_GET['profile']) && $_GET['profile']) {
				/*
				 * Profiling from within edit mode is not possible. We therefore
				 * redirect to the appropriate page in normal mode.
				 */
				$url = Aitsu_Db :: fetchOne('' .
				'select ' .
				'	concat(catlang.url, \'/\', artlang.urlname, \'.html\') as url ' .
				'from _art_lang as artlang ' .
				'left join _cat_art as catart on artlang.idart = catart.idart ' .
				'left join _cat_lang as catlang on catart.idcat = catlang.idcat and catlang.idlang = artlang.idlang ' .
				'where artlang.idartlang = :idartlang', array (
					':idartlang' => $_GET['id']
				));
				header('Location: ' . Aitsu_Registry :: get()->config->sys->webpath . $url . '?profile=1');
				exit;
			}

			return;
		}

		Aitsu_Registry :: get()->config = Aitsu_Config_Ini :: getInstance('clients/' . Aitsu_Mapping :: getIni());
		$this->configured = true;
	}

	protected function _ExecuteConfiguredPreInits() {

		Aitsu_Event :: raise('frontend.preInit', null);
	}

	protected function _InitializeSession() {

		if (!Aitsu_Config :: get('session.usefilesystem')) {
			if (Aitsu_Config :: get('memcached.enable')) {
				$saveHandler = new Aitsu_Session_MemcachedHandler();
			} else {
				Zend_Db_Table_Abstract :: setDefaultAdapter(Aitsu_Db :: getDb());
				$saveHandler = new Zend_Session_SaveHandler_DbTable(array (
					'name' => Aitsu_Db :: prefix('_aitsu_session'),
					'primary' => 'id',
					'modifiedColumn' => 'modified',
					'dataColumn' => 'data',
					'lifetimeColumn' => 'lifetime'
				));
			}

			Zend_Session :: setSaveHandler($saveHandler);
		}

		Zend_Session :: setOptions(array (
			'use_only_cookies' => 'off',
			'use_cookies' => 'on'
		));

		Zend_Session :: start(array (
			'name' => 'AITSU'
		));

		$this->session = new Zend_Session_Namespace('aitsu');

		Aitsu_Registry :: get()->session = $this->session;
	}

	protected function _CleanCache() {

		if (!isset ($_GET['clearcache'])) {
			return;
		}

		Aitsu_Ee_Cache_Page :: getInstance()->clearCache();

		if ($_GET['clearcache'] == 'all') {
			Aitsu_Cache :: getInstance()->clean();
			return;
		}

		if (empty ($_GET['clearcache'])) {
			return;
		}

		Aitsu_Cache :: getInstance()->clean(array (
			$_GET['clearcache']
		));
	}

	protected function _SetIniValues() {

		if (!isset (Aitsu_Registry :: get()->config->ini)) {
			return;
		}

		foreach (Aitsu_Registry :: get()->config->ini->toArray() as $entry) {
			ini_set($entry['key'], $entry['value']);
		}
	}

	protected function _HmacAuthentication() {

		$auth = Aitsu_Util_Request :: header('aitsuauth');

		if (!$auth)
			return;

		if (!preg_match('/([^\\s]*)\\s([^\\:]*)\\:(.*)/', $auth, $match))
			return;

		$auth = array (
			'type' => $match[1],
			'userid' => $match[2],
			'hash' => $match[3]
		);

		$uri = $_SERVER['REQUEST_URI'];
		$body = file_get_contents('php://input');

		$secret = Aitsu_Db :: fetchOne('' .
		'select password from _acl_user where login = :userid', array (
			':userid' => $auth['userid']
		));

		$checkHash = hash_hmac('sha1', $uri . $body, $secret);

		if ($auth['hash'] != $checkHash) {
			return;
		}

		Aitsu_Adm_User :: login($auth['userid'], $secret, true);
		Aitsu_Registry :: get()->session->user = Aitsu_Adm_User :: getInstance();
	}

	protected function _AuthenticateUser() {

		if (isset ($_REQUEST['logout'])) {
			Aitsu_Registry :: get()->session->user = null;
		}

		if (isset ($_POST['username']) && isset ($_POST['password'])) {
			if (Aitsu_Adm_User :: login($_POST['username'], $_POST['password'])) {
				Aitsu_Registry :: get()->session->user = Aitsu_Adm_User :: getInstance();
			}
		}
		elseif (isset (Aitsu_Registry :: get()->session->user)) {
			Aitsu_Adm_User :: rehydrate(Aitsu_Registry :: get()->session->user);
		}

		$user = Aitsu_Adm_User :: getInstance();

		if ((isset ($_GET['edit']) || isset ($_GET['preview'])) && ($user == null || !$user->isAllowed(array (
				'area' => 'article',
				'action' => 'update'
			)))) {
			header('HTTP/1.1 401 Access Denied');
			echo 'Access denied';
			exit ();
		}

		if ($user != null && isset ($_GET['structured'])) {
			Aitsu_Application_Status :: isStructured(true);
		}

		Aitsu_Registry :: isEdit(isset ($_GET['edit']));
		Aitsu_Registry :: isFront(!isset ($_GET['edit']));
		Aitsu_Application_Status :: isEdit(isset ($_GET['edit']));
		Aitsu_Application_Status :: isPreview(isset ($_GET['preview']));
		Aitsu_Application_Status :: setEnv('frontend');
	}

	protected function _SetBackendLang() {

		if (!Aitsu_Registry :: isEdit()) {
			return;
		}

		$availableLangs = array (
			'en',
			'de'
		);

		$lang = substr($this->session->belang, 0, 2);
		$lang = in_array($lang, $availableLangs) ? $lang : 'en';

		$adapter = new Zend_Translate('gettext', APPLICATION_PATH . '/languages/' . $lang . '/translate.mo', $lang);
		Aitsu_Registry :: get()->Zend_Translate = $adapter;
	}

	protected function _ExecuteConfiguredInits() {

		Aitsu_Event :: raise('frontend.init', null);
	}

	protected function _EvaluateRequest() {

		if (!isset ($_GET['id'])) {
			Aitsu_Bootstrap_EvalRequest :: run();
		} else {
			$data = Aitsu_Db :: fetchRowC(60 * 60, '' .
			'select ' .
			'	artlang.idart, ' .
			'	catart.idcat, ' .
			'	artlang.idlang, ' .
			'	artlang.idartlang, ' .
			'	lang.idclient, ' .
			'	catlang.public ' .
			'from _art_lang as artlang ' .
			'left join _cat_art as catart on artlang.idart = catart.idart ' .
			'left join _lang as lang on artlang.idlang = lang.idlang ' .
			'left join _cat_lang as catlang on catlang.idcat = catart.idcat and catlang.idlang = artlang.idlang ' .
			'where ' .
			'	artlang.idartlang = :idartlang', array (
				':idartlang' => $_GET['id']
			));

			Aitsu_Registry :: get()->env->idart = $data['idart'];
			Aitsu_Registry :: get()->env->idcat = $data['idcat'];
			Aitsu_Registry :: get()->env->idlang = $data['idlang'];
			Aitsu_Registry :: get()->env->lang = $data['idlang'];
			Aitsu_Registry :: get()->env->idartlang = $data['idartlang'];
			Aitsu_Registry :: get()->env->idclient = $data['idclient'];
			Aitsu_Registry :: get()->env->client = $data['idclient'];
		}

		$user = Aitsu_Adm_User :: getInstance();

		if (Aitsu_Application_Status :: isEdit() && $user != null && $user->isAllowed(array (
				'language' => Aitsu_Registry :: get()->env->idlang,
				'area' => 'article',
				'action' => 'update',
				'resource' => array (
					'type' => 'cat',
					'id' => Aitsu_Registry :: get()->env->idcat
				)
			))) {
			return;
		}

		if (isset (Aitsu_Registry :: get()->env->ispublic) && Aitsu_Registry :: get()->env->ispublic == 1) {
			/*
			 * No permission check necessary. Return.
			 */
			return;
		}

		if ($user != null && $user->isAllowed(array (
				'language' => Aitsu_Registry :: get()->env->idlang,
				'area' => 'frontend',
				'action' => 'view',
				'resource' => array (
					'type' => 'cat',
					'id' => Aitsu_Registry :: get()->env->idcat
				)
			))) {
			return;
		}

		/*
		 * The user seems not to be allowed to access the page. We therefore
		 * give him the possiblity to log in.
		 */
		Aitsu_Registry :: get()->env->idart = Aitsu_Config :: get('sys.loginpage');
		Aitsu_Bootstrap_EvalRequest :: setIdartlang(Aitsu_Config :: get('sys.loginpage'));
	}

	protected function _LockApplicationStatus() {

		Aitsu_Application_Status :: setEnv('front');
		Aitsu_Application_Status :: lock();
	}

	protected function _RenderOutput() {

		$this->pageContent = '<script type="application/x-aitsu" src="Template:Root"></script>';
	}

	protected function _ExecuteConfiguredTransformations() {

		Aitsu_Event :: raise('frontend.dispatch', array (
			'bootstrap' => $this
		));
	}

	protected function _ExecuteConfiguredUrlRewriting() {

		if (!Aitsu_Registry :: get()->config->rewrite->modrewrite) {
			return;
		}

		$obj = call_user_func(array (
			Aitsu_Registry :: get()->config->rewrite->controller,
			'getInstance'
		));

		$this->pageContent = $obj->rewriteOutput($this->pageContent);
	}

	protected function _CacheIntoTheFileSystem() {

		if (!Aitsu_Registry :: get()->config->cache->page->enable) {
			/*
			 * Cache is disabled.
			 */
			return;
		}

		if (Aitsu_Adm_User :: getInstance() != null) {
			/*
			 * Cache is disabled.
			 */
			return;
		}

		Aitsu_Ee_Cache_Page :: getInstance()->saveFs($this->pageContent);
	}

	protected function _TriggerIndexing() {

		Aitsu_Event :: raise('frontend.indexing', array (
			'bootstrap' => $this
		));
	}

	protected function _ProfileExecution() {

		$profile = Aitsu_Profiler :: get();
		if ($profile !== false) {
			$this->pageContent = $profile;
		}
	}

	protected function _TriggerEnd() {

		Aitsu_Event :: raise('frontend.end', null);
	}

	public static function run() {

		static $running = false;

		if ($running) {
			throw new Exception('The bootstrap may only run once for each request.');
		}

		$instance = new self();

		if (getenv('AITSU_DEBUG') == 'on') {
			$instance->debug = true;
		}

		try {
			$counter = 0;
			foreach (get_class_methods($instance) as $phase) {
				if (substr($phase, 0, strlen('_')) == '_') {
					if ($instance->configured) {
						$id = substr($phase, 1);
						Aitsu_Profiler :: profile($id, null, 'system');
						call_user_func(array (
							$instance,
							$phase
						));
						Aitsu_Profiler :: profile($id, null, 'system');
					} else {
						call_user_func(array (
							$instance,
							$phase
						));
					}
				}
				if ($instance->debug && isset ($_GET['step']) && $counter >= (int) $_GET['step']) {
					echo '<p>Execution stopped after executing <strong>' . $phase . '</strong>.</p>';
					echo '<p>Next step: <a href="' . $_SERVER['PHP_SELF'] . '?step=' . ($counter +1) . '"><strong>Execute</strong></a>.</p>';
					echo '<pre>' . var_export($instance, true) . '</pre>';
					exit ();
				}
				$counter++;
			}
		} catch (Exception $e) {
			trigger_error('Exception in ' . __FILE__ . ' on line ' . __LINE__ . ': ' . $e->getMessage());
			trigger_error("Stack trace: \n" . $e->getTraceAsString());
			exit ();
		}

		return $instance;
	}

	public function getOutput() {

		if (Aitsu_Application_Status :: isStructured()) {
			return Aitsu_Transformation_StructuredTo :: xml($this->pageContent);
		}

		$expire = Aitsu_Registry :: getExpireTime();

		if (empty ($expire) || Aitsu_Application_Status :: isEdit()) {
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		} else {
			header("Cache-Control: max-age=" . $expire);
			header("Pragma: public");
			header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expire) . ' GMT');
		}

		return $this->pageContent;
	}

}