<?php


/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 * 
 * {@id $Id: Class.php 19836 2010-11-11 18:22:33Z akm $}
 */

class ModulesPluginController extends Aitsu_Adm_Plugin_Controller {

	protected $_modules = array ();
	protected $_modulesPlain = array ();

	public function init() {

		$modules = array ();

		/*
		 * Read standard modules
		 */
		$files = Aitsu_Util_Dir :: scan(realpath(APPLICATION_PATH . '/../library/Aitsu/Ee/Module'), 'Class.php');
		$baseLength = strlen(realpath(APPLICATION_PATH . '/../library/')) + 1;
		foreach ($files as $file) {
			include_once ($file);
			$class = str_replace('/', '_', substr($file, $baseLength, -4));
			$detail = call_user_func(array (
				$class,
				'about'
			));
			$detail->className = $class;
			$detail->source = $file;
			$detail->isReady = call_user_func(array (
				$class,
				'isReady'
			));
			$details[] = $detail;
		}

		/*
		 * Read skin modules
		 */
		$files = Aitsu_Util_Dir :: scan(APPLICATION_PATH . '/skins', 'Class.php');
		foreach ($files as $file) {
			$content = file_get_contents($file);
			preg_match('/^\\s*class\\s*([^\\s]*)/m', $content, $match);
			$className = $match[1];
			$newClassName = uniqid('class');
			$content = str_replace($className, $newClassName, $content);
			eval ('?>' . $content);
			$detail = call_user_func(array (
				$newClassName,
				'about'
			));
			if (empty ($detail->name)) {
				$parts = explode('_', $className);
				$detail->name = $parts[count($parts) - 2];
			}
			preg_match('@/skins/(.*?)/module/\\w*/Class.php@', $file, $match);
			$detail->name .= ' (' . $match[1] . ')';
			$detail->className = $className;
			$detail->source = $file;
			$detail->isReady = call_user_func(array (
				$newClassName,
				'isReady'
			));
			if (!empty ($detail->id)) {
				$details[] = $detail;
			}
		}

		usort($details, array (
			'self',
			'compare'
		));

		foreach ($details as $detail) {
			if (isset ($detail->type)) {
				if (is_array($detail->type)) {
					foreach ($detail->type as $type) {
						$modules[$type][] = $detail;
					}
				} else {
					$modules[$detail->type][] = $detail;
				}
				$modules['All'][$detail->id] = $detail;
			} else {
				$modules['Uncategorized'][] = $detail;
			}
			$this->_modulesPlain[] = $detail;
		}

		ksort($modules);

		$this->view->placeholder('left')->set($this->view->partial('subnav.phtml', array (
			'modules' => $modules,
			'category' => $this->getRequest()->getParam('category')
		)));

		$this->_modules = $modules;
	}

	public function indexAction() {

		$this->view->modules = $this->_modules;
		$this->view->category = $this->getRequest()->getParam('category');
	}

	public function installAction() {

		$this->_helper->viewRenderer->setNoRender(true);

		$this->view->category = $this->getRequest()->getParam('category');
		$id = $this->getRequest()->getParam('module');

		try {
			foreach ($this->_modulesPlain as $module) {
				if ($module->id == $id) {
					include_once ($module->source);
					call_user_func(array (
						$module->className,
						'install'
					));
					break;
				}
			}
		} catch (Exception $e) {
			/* nothing to do, installation failed */
		}

		$this->init();

		$this->view->module = $module;
		echo $this->view->render('detail.phtml');
	}

	private static function compare($a, $b) {

		return $a->name > $b->name;
	}

	public function detailAction() {

		$id = $this->getRequest()->getParam('module');
		foreach ($this->_modulesPlain as $module) {
			if ($module->id == $id) {
				$this->view->module = $module;
				break;
			}
		}
	}
}