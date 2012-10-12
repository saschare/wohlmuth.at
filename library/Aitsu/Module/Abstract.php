<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2011, w3concepts AG
 */

abstract class Aitsu_Module_Abstract {

	protected $_allowEdit = true;

	/*
	 * Internal ID used for caching purposes. The value is
	 * set the first time _get is called.
	 */
	protected $_id;

	/*
	 * ID suffix to be added to the internal ID.
	 */
	protected $_idSuffix = '';

	protected $_type = null;
	protected $_view = null;
	protected $_context = null;
	protected $_index = null;
	protected $_params = null;
	protected $_moduleName = '';

	/*
	 * _isVolatile flags the cached output to be volatile in the
	 * sense that each and every single publish event should result
	 * in a loss of the cached data. _isVolatile should be set to
	 * true, if the output is dependend on data outside the scope of
	 * the current article (page).
	 */
	protected $_isVolatile = false;

	/*
	 * Normally the caching mechanism is disabled as soon as the visitor
	 * is logged in the system to prevent to persist data that is sensitive 
	 * or specific for a particular user. To allow caching of the modules
	 * output even if the user is logged in, set the flag to true.
	 */
	protected $_cacheIfLoggedIn = false;

	/*
	 * If set to true, the cache is build off context of the current article.
	 * This has two consequences. The cache is availabe all over the page and
	 * the cache will not be destroyed on page edit.
	 */
	protected $_disableCacheArticleRelation = false;

	/*
	 * Indicates that the output of the module is a block element. Set this
	 * value to false, if the output is not a block element to allow the 
	 * system to render the output accordingly in the edit mode.
	 */
	protected $_isBlock = true;

	/*
	 * If $_renderOnlyAllowed is set to false (default), the module may not
	 * be rendered by adding the renderOnly get parameter to the URL. If set
	 * to true, the module may be executed by adding the mentioned parameter in
	 * one of the following two ways:
	 * 
	 * (1) http(s)://my.domain.tld/my/page/(moduleName)
	 * (2) http(s)://my.domain.tld/my/page/?renderOnly=moduleName
	 * 
	 * where moduleName is the name of the module as it is used as a shortcode
	 * (i.e. with or without an index separated by a colon).
	 */
	protected $_renderOnlyAllowed = false;

	protected static function _getInstance($className) {

		$instance = new $className ();

		$className = str_replace('_', '.', $className);
		$className = preg_replace('/^(?:Skin\\.Module|Module)\\./', "", $className);
		$className = preg_replace('/\\.Class$/', "", $className);

		if (isset ($_GET['renderOnly']) && $className == substr($_GET['renderOnly'], 0, strlen($className)) && !$instance->_renderOnlyAllowed) {
			throw new Aitsu_Security_Module_RenderOnly_Exception($className);
		}

		$instance->_moduleName = $className;

		return $instance;
	}

	public static function init($context, $instance = null) {

		$output = '';

		$instance = is_null($instance) ? self :: _getInstance($context['className']) : $instance;

		/*
		 * Set to non-block, if _isBlock is set to false.
		 */
		if (!$instance->_isBlock) {
			Aitsu_Content_Edit :: isBlock(false);
		}

		$instance->_context = $context;

		$instance->_context['rawIndex'] = $instance->_context['index'];
		$instance->_context['index'] = preg_replace('/[^a-zA-Z_0-9]/', '_', $instance->_context['index']);
		$instance->_context['index'] = str_replace('.', '_', $instance->_context['index']);

		$instance->_index = empty ($instance->_context['index']) ? 'noindex' : $instance->_context['index'];

		if (!empty ($instance->_context['params'])) {
			$instance->_params = Aitsu_Util :: parseSimpleIni($instance->_context['params']);
		}

		/*
		 * Suppress edit option, if _allowEdit is set to false or the
		 * edit parameter is set to false.
		 */
		if (!$instance->_allowEdit || (isset ($instance->_params->edit) && !$instance->_params->edit)) {
			Aitsu_Content_Edit :: noEdit($instance->_moduleName, true);
		}

		/*
		 * Execution of the _init method is done, even a 
		 * valid cache is available.
		 */
		$output = $instance->_init();

		if ($instance->_cachingPeriod() > 0) {
			if ($instance->_get($context['className'], $output)) {
				return $output;
			}
		}

		/*
		 * Execution of the _main method is only done, if caching
		 * is disabled or there is no valid cache.
		 */
		$output .= $instance->_main();

		/*
		 * Do the transformation of the output.
		 */
		$output = $instance->_transformOutput($output);

		if ($instance->_cachingPeriod() > 0) {
			$instance->_save($output, $instance->_cachingPeriod());
		}

		if (Aitsu_Application_Status :: isEdit()) {
			$maxLength = 60;
			$index = strlen($context['index']) > $maxLength ? substr($context['index'], 0, $maxLength) . '...' : $context['index'];

			if (trim($output) == '' && $instance->_allowEdit) {
				if (preg_match('/^Module_(.*?)_Class$/', $context['className'], $match)) {
					$moduleName = str_replace('_', '.', $match[1]);
				}
				elseif (preg_match('/^Skin_Module_(.*?)_Class$/', $context['className'], $match)) {
					$moduleName = str_replace('_', '.', $match[1]);
				} else {
					$moduleName = 'UNKNOWN';
				}
				if ($instance->_isBlock) {
					return '' .
					'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
					'<div style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' .
					'	<div style="height:15px; background-color: #CCC; color: white; font-size: 11px; padding:2px 5px 0 5px;">' .
					'		<span style="font-weight:bold; float:left;">' . $index . '</span><span style="float:right;">Module <span style="font-weight:bold;">' . $moduleName . '</span></span>' .
					'	</div>' .
					'</div>';
				} else {
					return '' .
					'<span style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' .
					'	' . $moduleName . ' :: ' . $index .
					'</span>';
				}
			}

			if (!$instance->_isBlock) {
				return '' .
				'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
				'<span style="border:1px dashed #CCC; padding:2px 2px 2px 2px;">' . $output . '</span>';
			}

			if (isset ($instance->_params->suppressWrapping) && $instance->_params->suppressWrapping) {
				return $output;
			}

			return '' .
			'<code class="aitsu_params" style="display:none;">' . $context['params'] . '</code>' .
			'<div>' . $output . '</div>';
		}

		return $output;
	}

	protected function _init() {

		return '';
	}

	/**
	 * _transformOutput lets the module transform or replace the generated output
	 * before it is placed into the sourrounding document. This is especially helpfull
	 * to suppress output in edit mode in extended abstract classes such as the
	 * MicroApp abstract class.
	 * @param String $output The output of the module.
	 * @return String The transformed or replaced output.
	 */
	protected function _transformOutput($output) {

		return $output;
	}

	protected function _main() {

		return '';
	}

	protected function _cachingPeriod() {

		return 0;
	}

	/**
	 * Overwrite this method accordingly if the module has
	 * dependencies to ensure it returns true only, if all
	 * the dependencies evaluate to true.
	 * 
	 * @return Boolean Returns true, if the module is ready to be used. False otherwise.
	 */
	public static function isReady() {

		return true;
	}

	/**
	 * This method is called, if the module returns false on isReady
	 * call and the user asks for installation. After a call of install
	 * the method isReady should return true from then on.
	 */
	public static function install() {

		return true;
	}

	protected function _getView($view = null) {

		if ($this->_view != null) {
			return $this->_view;
		}

		$class = get_class($this);

		$search = array (
			'_Class',
			'_'
		);
		$replace = array (
			'',
			'/'
		);

		$modulePath = str_replace($search, $replace, $class);

		$view = $view == null ? new Zend_View() : $view;

		$search = array (
			'Aitsu/Ee/Module/',
			'Local/Module/',
			'Skin/Module/',
			'Module/'
		);
		$replace = array (
			'',
			'',
			'',
			''
		);

		$skinModulePath = APPLICATION_PATH . "/skins/" . (isset (Aitsu_Registry :: get()->config->skin) ? Aitsu_Registry :: get()->config->skin : 'x') . "/module/" . str_replace($search, $replace, $modulePath);
		
		if (count(preg_grep("/^Class.php$|.phtml$/", Aitsu_Util_Dir :: scan($skinModulePath))) > 0) {
			$view->setScriptPath($skinModulePath);
		}
		elseif (file_exists(APPLICATION_PATH . '/modules/' . str_replace('_', '/', substr($class, 7, strlen($class) - 13)))) {
			$view->setScriptPath(APPLICATION_PATH . '/modules/' . str_replace('_', '/', substr($class, 7, strlen($class) - 13)));
		} else {
			$path = realpath(APPLICATION_PATH . '/../library');
			$view->setScriptPath($path . '/' . $modulePath);
		}

		return $view;
	}

	protected function _get($id, & $output) {

		$id = $this->_normalizeIndex($id);

		if ($this->_disableCacheArticleRelation) {
			$lang = Aitsu_Application_Status :: isEdit() ? Aitsu_Registry :: get()->session->currentLanguage : Aitsu_Registry :: get()->env->idlang;
			$this->_id = $id . '_lang' . $lang . '_' . $this->_index . '_' . $this->_idSuffix;
		} else {
			$this->_id = $id . '_' . Aitsu_Registry :: get()->env->idartlang . '_' . $this->_index . '_' . $this->_idSuffix;
		}

		$cache = Aitsu_Cache :: getInstance($this->_id, $this->_cacheIfLoggedIn);

		if (Aitsu_Registry :: isEdit() && !$this->_cacheIfLoggedIn) {
			$cache->remove();
			return false;
		}

		if ($cache->isValid()) {
			$output = $cache->load();
			return true;
		}

		return false;
	}

	protected function _remove($id = null) {

		$id = $this->_normalizeIndex($id);

		if ($id != null) {
			$this->_id = $id . '_' . Aitsu_Registry :: get()->env->idartlang;
		}

		Aitsu_Cache :: getInstance($this->_id)->remove();
	}

	protected function _save($data, $lifeTime = null) {

		if ($data == null) {
			$data = '';
		}

		if (!is_string($data)) {
			throw new Exception('non-string data to be cached in ' . get_class($this));
		}

		$cache = Aitsu_Cache :: getInstance($this->_id, $this->_cacheIfLoggedIn);

		if (Aitsu_Registry :: isEdit() && !$this->_cacheIfLoggedIn) {
			return false;
		}

		$tags = array ();

		if (!empty ($this->_type)) {
			$tags[] = 'type_' . $this->_type;
		}

		if (!$this->_disableCacheArticleRelation) {
			$tags[] = 'cat_' . Aitsu_Registry :: get()->env->idcat;
			$tags[] = 'art_' . Aitsu_Registry :: get()->env->idart;
		}

		if ($this->_isVolatile) {
			/*
			 * The volatile tag is set to make sure the cached output
			 * is deleted on every publish event. Refer to the comment
			 * on the class member _isVolatile for details.
			 */
			$tags[] = 'volatile';
		}

		if (!empty ($lifeTime)) {
			if ($lifeTime == 'eternal') {
				$lifeTime = 60 * 60 * 24 * 365; // one year
			}
			$cache->setLifetime($lifeTime);
		}

		$cache->save($data, $tags);
	}

	protected function _getTemplates() {

		$return = array ();

		$templates = $this->_getView()->getScriptPaths();

		$templateFiles = array ();
		foreach ($templates as $template) {
			$files = glob($template . '*.phtml');
			if (!empty ($files)) {
				$templateFiles = array_merge($templateFiles, $files);
			}
		}

		foreach ($templateFiles as $file) {
			$content = file_get_contents($file);
			if (preg_match('/^<\\!\\-{2}\\s*(.*?)\\s*\\-{2}>/', $content, $match)) {
				$return[$match[1]] = substr(basename($file), 0, -6);
			}
		}

		return $return;
	}

	private function _normalizeIndex($id) {

		return preg_replace('/[^a-zA-Z_0-9]/', '', $id);
	}

	protected static function _getChildrenOf($type, & $result, & $subSet, $in = false) {

		foreach ($subSet as $key => $value) {
			$key = str_replace('_', '.', $key);
			if ($in || $type == $key) {
				$result[$key] = $key;
			}
			if (is_array($value)) {
				self :: _getChildrenOf($type, $result, $value, $in || $type == $key);
			}
		}
	}

}