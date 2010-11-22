<?php


/**
 * Abstract module class.
 * 
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Abstract.php 18351 2010-08-26 07:41:14Z akm $}
 */

abstract class Aitsu_Ee_Module_Abstract {

	protected $id;
	protected $type = null;
	protected $_view = null;

	abstract public static function init($context);

	public static function about() {

		return (object) array (
			'name' => '',
			'description' => '',
			'author' => (object) array (
				'name' => '',
				'copyright' => 'w3concepts AG'
			),
			'version' => 'not specified',
			'status' => 'stable',
			'url' => null
		);
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

	protected function _getView() {

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

		$view = new Zend_View();

		$search = array (
			'Aitsu/Ee/Module/',
			'Local/Module/',
			'Skin/Module/'
		);
		$replace = array (
			'',
			'',
			''
		);
		$skinModulePath = APPLICATION_PATH . "/skins/" . (isset (Aitsu_Registry :: get()->config->skin) ? Aitsu_Registry :: get()->config->skin : 'x') . "/module/" . str_replace($search, $replace, $modulePath);

		if (file_exists($skinModulePath)) {
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

	/**
	 * This function is deprecated. Please use _get to receive and _save to persist
	 * cache data.
	 * @deprecated 0.9.2 - 2010-07-27
	 */
	protected function _cache($id, $data = null, $lifeTime = null) {

		$id = Aitsu_Registry :: get()->env->idartlang . '_' . $id;
		$cache = Aitsu_Cache :: getInstance($id);

		if (Aitsu_Registry :: isEdit()) {
			$cache->remove();
			return false;
		}

		if (empty ($data)) {
			return $cache->load();
		}

		if (!empty ($this->type)) {
			$tags[] = 'type_' . $this->type;
		}
		$tags[] = 'cat_' . Aitsu_Registry :: get()->env->idcat;
		$tags[] = 'art_' . Aitsu_Registry :: get()->env->idart;

		if (!empty ($lifeTime)) {
			if ($lifeTime == 'eternal') {
				$lifeTime = 60 * 60 * 24 * 365; // one year
			}
			$cache->setLifetime($lifeTime);
		}

		$cache->save($data, $tags);
	}

	protected function _get($id, & $output, $overwriteDisable = false) {

		$this->id = $id . '_' . Aitsu_Registry :: get()->env->idartlang;
		$cache = Aitsu_Cache :: getInstance($this->id, $overwriteDisable);

		if (Aitsu_Registry :: isEdit()) {
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

		if ($id != null) {
			$this->id = $id . '_' . Aitsu_Registry :: get()->env->idartlang;
		}

		Aitsu_Cache :: getInstance($this->id)->remove();
	}

	protected function _save($data, $lifeTime = null) {

		if ($data == null) {
			$data = '';
		}

		if (!is_string($data)) {
			throw new Exception('non-string data to be cached in ' . get_class($this));
		}

		$cache = Aitsu_Cache :: getInstance($this->id);

		if (Aitsu_Registry :: isEdit()) {
			return false;
		}

		if (!empty ($this->type)) {
			$tags[] = 'type_' . $this->type;
		}
		$tags[] = 'cat_' . Aitsu_Registry :: get()->env->idcat;
		$tags[] = 'art_' . Aitsu_Registry :: get()->env->idart;

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
}
?>